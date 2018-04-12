<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 16/03/2018
 * Time: 19:15
 */

namespace Alagunto\EzSchedules\Contracts;

use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

abstract class RepetitionStrategy
{
    public $schedule_items_model;

    /** @var Carbon $starts_at */
    public $starts_at = null;
    /** @var Carbon $ends_at */
    public $ends_at = null;
    public $time;
    public $time_to;

    public function __construct() {
        $this->serializer = new \SuperClosure\Serializer(null, config("app.key"));
    }

    public function setItemsModel($model) {
        $this->schedule_items_model = $model;
    }

    /**
     * Generates & provides ScheduleItems for a given time range based on this strategy
     * @var Carbon $from
     * @var Carbon $to
     */
    public abstract function provide($from, $to);

    public function setStartsAt(Carbon $from) {
        $this->starts_at = $from;
    }

    public function setEndsAt(Carbon $to) {
        $this->ends_at = $to;
    }

    public function save(RepetitionStrategiesStorage $storage) {
        if(is_null($this->starts_at))
            throw new \InvalidArgumentException("Please provide 'from' -- when the repetition starts");
        $storage->starts_at = $this->starts_at;
        $storage->ends_at = $this->ends_at;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setTimeTo($time) {
        $this->time_to = $time;
    }

    public function restoreFromStorage(RepetitionStrategiesStorage $storage) {
        $this->time = $storage->time;
        $this->time_to = $storage->time_to;

        $this->starts_at = $storage->starts_at;
        $this->ends_at = $storage->ends_at;
        $this->schedule_items_model = $storage->item_model;
    }
}