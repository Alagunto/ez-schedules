<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 23/03/2018
 * Time: 20:42
 */

namespace Alagunto\EzSchedules\Proxies;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduledRepetition
{
    /** @var RepetitionStrategiesStorage */
    protected $storage;

    /** @var RepetitionStrategy */
    protected $repetition_strategy;

    public function __construct(RepetitionStrategiesStorage $model) {
        $this->storage = $model;

        $this->repetition_strategy = new $this->storage["repetition_strategy"];
        $this->repetition_strategy->restoreFromStorage($this->storage);
    }

    public function getRepetitionStrategyClassname() {
        return $this->storage["repetition_strategy"];
    }

    /**
     * @return Carbon
     */
    public function getStartsAt() {
        return $this->repetition_strategy->starts_at;
    }

    /**
     * @return Carbon
     */
    public function getEndsAt() {
        return $this->repetition_strategy->ends_at;
    }

    public function getTime() {
        return $this->repetition_strategy->time;
    }

    public function getScheduleItemClassname() {
        return $this->repetition_strategy->schedule_items_model;
    }

    /**
     * @param Carbon $at
     * @throws \Exception
     */
    public function cancel(Carbon $at) {
        throw new \Exception("Not implemented yet");
    }
}