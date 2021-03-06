<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 06/03/2018
 * Time: 02:27
 */

namespace Alagunto\EzSchedules\Builder;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Alagunto\EzSchedules\Eacher\EacherRepetitionStrategy;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Alagunto\EzSchedules\SchedulesManager;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Carbon\Carbon;
use \Exception;
use Illuminate\Database\Eloquent\Model;

class ScheduleCreatorQueryBuilder
{
    protected $model_class;

    /** @var RepetitionStrategy */
    protected $rule;
    protected $put_closure;
    protected $put_params;
    protected $priority = 0;
    protected $whens = [];
    protected $transcendent = false;
    protected $time;
    protected $time_to;
    protected $colliders = [];

    protected $once = false;

    /** @var Model $item */
    protected $item = null;

    /**
     * ScheduleCreatorQueryBuilder constructor.
     * @param $model_class
     * @throws Exception
     */
    public function __construct($model_class) {
        $this->model_class = $model_class;
        $this->serializer = new \SuperClosure\Serializer(null, config("app.key"));
    }

    public function collideBy($colliders) {
        $this->colliders = $colliders;

        return $this;

        // TODO: check those colliders to give instant errors
    }

    public function once() {
        $this->once = true;
        $this->item = new $this->model_class;

        return $this;
    }

    public function each($what) {
        $this->rule = new EacherRepetitionStrategy();
        $this->rule->setEach($what);

        return $this;
    }

    /**
     * @param $time
     * @return $this
     * @throws Exception
     */
    public function at($time) {
        if($this->once) {
            if(is_string($time))
                $time = Carbon::parse($time);
            if(!($time instanceof Carbon))
                throw new Exception("Cannot parse that time, buddy");

            $this->time = clone $time;
            $this->item->starts_at = $this->time;
        } else {
            $this->failWithoutRule();
            $this->time = $time;
            $this->rule->setTime($time);
        }

        return $this;
    }

    public function endsAt($time) {
        $this->time_to = $time;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function transcendent() {
        if(!$this->rule)
            throw new Exception("Only repeated actions can be transcendent");

        $this->transcendent = true;

        return $this;
    }

    /**
     * @param $strategy
     * @return $this
     * @throws Exception
     */
    public function repeat($strategy) {
        if($this->once)
            throw new Exception("But do I repeat or do it once?");

        $this->rule = new $strategy();

        if(!$this->rule instanceof RepetitionStrategy)
            throw new Exception("You probably forgot to inherit your strategy from RepetitionStrategy. Please, do it :)");

        return $this;
    }

    /**
     * @param $priority
     * @return $this
     * @throws Exception
     */
    public function withPriority($priority) {
        $this->failWithoutRule()->priority = $priority;

        return $this;
    }
    
    public function if(Callable $closure) {
        $this->whens[] = $this->serializer->serialize($closure);

        return $this;
    }

    public function andIf(Callable $closure) {
        $this->if($closure);

        return $this;
    }

    /**
     * @param $from
     * @return $this
     * @throws Exception
     */
    public function from($from) {
        if(is_string($from))
            $from = Carbon::parse($from);

        if(!($from instanceof Carbon))
            throw new Exception("Cannot parse 'from' argument");

        if($this->once)
            $this->item->starts_at = clone $from;
        else
            $this->failWithoutRule()->rule->setStartsAt(clone $from);

        return $this;
    }

    /**
     * @param $to
     * @return $this
     * @throws Exception
     */
    public function to($to) {
        if(is_string($to))
            $to = Carbon::parse($to);

        if(!($to instanceof Carbon))
            throw new Exception("Cannot parse 'to' argument");

        if($this->once)
            $this->item->ends_at = clone $to;
        else
            $this->failWithoutRule()->rule->setEndsAt($to);

        return $this;
    }

    public function put($items_or_closure) {
        if(is_array($items_or_closure)) {
            $this->put_params = $items_or_closure;
        } else {
            $this->put_closure = $items_or_closure;
        }

        return $this;
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function save() {
        if($this->once) {
            return $this->saveAsOnce();
        } else {
            return $this->saveAsRepeated();
        }
    }

    private function saveAsOnce() {
        if($this->put_params)
            $this->item->fill($this->put_params);

        $this->item->repetition_id = null;

        if($this->put_closure) {
            $result = ($this->put_closure)($this->item);
            $this->item->fill($result);
        }

        $this->item->save();
        return $this->item;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function saveAsRepeated() {
        $this->failWithoutRule();

        $storage = new RepetitionStrategiesStorage();
        $storage->repetition_strategy = get_class($this->rule);
        $storage->item_model = $this->model_class;
        $storage->put_params = $this->put_params;
        $storage->priority = $this->priority;
        $storage->time = $this->time;
        $storage->time_to = $this->time_to;
        $storage->params->core->whens = $this->whens;
        $storage->params->core->colliders = $this->colliders;

        if($this->put_closure) {
            $storage->put_closure = $this->serializer->serialize($this->put_closure);
        }

        $this->rule->save($storage);

        return $storage->save();
    }

    /**
     * @return $this
     * @throws Exception
     */
    private function failWithoutRule() {
        if(!$this->rule)
            throw new Exception("Wait, you have to set rule with 'repeat' or 'each' before doing that");

        return $this;
    }
}