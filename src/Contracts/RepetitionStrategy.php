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
    protected $schedule_items_model;

    protected $starts_at = null;
    protected $ends_at = null;

    public function __construct() {
        $this->serializer = new \SuperClosure\Serializer(null, config("app.key"));
    }

    public function setItemsModel($model) {
        $this->schedule_items_model = $model;
    }

    /**
     * Generates & provides ScheduleItems for a given time range based on this strategy
     */
    public abstract function provide(Carbon $from, Carbon $to);

    public function setStartsAt(Carbon $from) {
        $this->starts_at = $from;
    }

    public function setEndsAt(Carbon $to) {
        $this->ends_at = $to;
    }

    public function save(RepetitionStrategiesStorage $storage) {
        $storage->starts_at = $this->starts_at;
        $storage->ends_at = $this->ends_at;
    }

    public function restoreFromStorage(RepetitionStrategiesStorage $storage) {
        $this->starts_at = $storage->starts_at;
        $this->ends_at = $storage->ends_at;
        $this->schedule_items_model = $storage->item_model;
    }
}