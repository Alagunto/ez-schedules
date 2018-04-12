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
use Alagunto\EzSchedules\Test\ScheduleItem;
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
     * @param Carbon|string $at
     * @throws \Exception
     */
    public function cancel($at, $mode = "delete_futures") {
        if(is_string($at))
            $at = Carbon::parse($at);
        if(!($at instanceof Carbon))
            throw new \InvalidArgumentException("Please provide carbon instance as 'at' attribute (or at least something carbon-parsable)");

        $this->storage->ends_at = $at;
        $this->repetition_strategy->ends_at = $at;
        $this->storage->save();

        if($mode == "delete_futures") {
            return $this->deleteGeneratedAfter($at);
        } elseif($mode == "delete_all") {
            return ScheduleItem::raw()
                ->where("repetition_id", $this->storage->id)
                ->delete();
        } elseif($mode == "leave_seen") {
            // Do nothing, wow!
        } elseif($mode == "leave_edited")
            throw new \Exception("Not implemented yet");
        else {
            throw new \InvalidArgumentException("Sorry, mode is unrecognized");
        }
    }

    public function deleteGeneratedAfter($moment) {
        return ScheduleItem::raw()
            ->where("repetition_id", $this->storage->id)
            ->where("starts_at", ">", $moment)
            ->delete();
    }
}