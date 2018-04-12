<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 08/03/2018
 * Time: 18:12
 */

namespace Alagunto\EzSchedules;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class SchedulesManager
{
    /**
     * @param string $class
     * @param $from
     * @param $to
     * @throws \Exception
     */
    public function resolveScheduleItemsForModel(string $class, $from, $to) {
        $storages = RepetitionStrategiesStorage::query()
            ->whereNested(function(Builder $query) use ($from, $to) {
                $query->where("starts_at", "<=", $to)
                    ->orWhere("starts_at", null);
            })
            ->whereNested(function(Builder $query) use ($from, $to) {
                $query->where("ends_at", ">=", $from)
                    ->orWhere("ends_at", null);
            })
            ->where("item_model", $class)
            ->orderBy("priority")
            ->get();

        $packs = [];

        foreach($storages as $storage) {
            /** @var RepetitionStrategy $repetition_strategy */
            $repeater = new RepeatedItemsGenerator($storage);


            $storage_generation_starts_at = Carbon::parse($storage->starts_at);
            $generate_from = clone $from;
            if($storage_generation_starts_at->greaterThan($generate_from))
                $generate_from = clone $storage_generation_starts_at;

            $generate_to = clone $to;
            if(!is_null($storage->ends_at)) {
                $storage_generation_ends_at = Carbon::parse($storage->ends_at);
                if ($storage_generation_ends_at->lessThan($generate_to))
                    $generate_to = clone $storage_generation_ends_at;
            }

            $generated_items = $repeater->generate($generate_from, $generate_to);

            /** @var Model $item */
            foreach($generated_items as $item) {
                $item->repetition_id = $storage->id;
            }

            $items_packer = new RepeatedItemsPacker($storage, $generated_items);
            $packs = $items_packer->packWith($packs);
        }

        $resulting_items = [];

        foreach($packs as $pack) {
            foreach($pack["items"] as $item) {
                /** @var IsAScheduleItem $item */
                $item->save();
                $resulting_items[] = $item;
            }
        }

        return $resulting_items;
    }
}