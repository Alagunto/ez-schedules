<?php

namespace Alagunto\EzSchedules;
use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionFunction;

/**
 * Class RepeatedItemsGenerator
 * Generates items in a given time range defined by the repetition strategy and
 * it's params stored in the storage container
 * @package Alagunto\EzSchedules
 */
class RepeatedItemsGenerator
{
    protected $storage;
    protected $serializer;

    public function __construct(RepetitionStrategiesStorage $storage) {
        $this->storage = $storage;
        $this->serializer = new \SuperClosure\Serializer(null, config("app.key"));
    }

    public function generate(Carbon $from, Carbon $to) {
        // Create an instance of strategy
        /** @var RepetitionStrategy $strategy */
        $strategy = new $this->storage["repetition_strategy"];

        // Feed the strategy the container
        $strategy->restoreFromStorage($this->storage);

        $generated_items = collect($strategy->provide($from, $to));

        $model = $this->storage->item_model;

        /** @var Collection $already_existing_items */
        $already_existing_items = $model::raw()->where('starts_at', '>=', $from)->where('starts_at', '<=', $to)->get();

        $duplicates = collect([]);

        // Ensuring we don't re-create those items that exist
        $generated_items = $generated_items->filter(function($item) use ($already_existing_items, $duplicates) {
            $duplicate = $already_existing_items->where("starts_at", $item->starts_at)
                ->where("repetition_id", $this->storage->id)
                ->first();

            if(!is_null($duplicate)) {
                $duplicates->push($duplicate);
                return false;
            }

            return true;
        });

        // Filter each generated item with whens
        foreach($this->storage->params->core["whens"] ?? [] as $when) {
            $closure = $this->serializer->unserialize($when);

            $generated_items->filter(function($model) use ($closure, $from, $to) {
                $reflection = new ReflectionFunction($closure);
                $arguments  = $reflection->getParameters();
                $arguments_count = count($arguments);

                if($arguments_count == 0)
                    return $closure();
                elseif($arguments_count == 1)
                    return $closure($model);
                elseif($arguments_count == 2)
                    return $closure($model, $this->storage);
                elseif($arguments_count == 3)
                    return $closure($model, $this->storage, $from);
                elseif($arguments_count == 4)
                    return $closure($model, $this->storage, $from, $to);
                else
                    throw new \Exception("Too much arguments for the closure");
            });
        }

        // For each generated item apply storage params
        if(!empty($this->storage->put_params))
            $generated_items = $generated_items
                ->map(function(Model $item) {
                    return $item->fill($this->storage->put_params);
                });


        if(!empty($this->storage->put_closure)) {
            $closure = $this->serializer->unserialize($this->storage->put_closure);

            $generated_items = $generated_items->map(function($value) use ($closure) {
                /** @var Model $value */
                // TODO: feed closure more info
                $put = $closure($value);
                return $value->fill($put);
            });
        }

        return $generated_items->merge($duplicates);
    }
}