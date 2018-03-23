<?php

namespace Alagunto\EzSchedules;

use Alagunto\EzSchedules\Builder\ProxifiedRepetitionsReaderQueryBuilder;
use Alagunto\EzSchedules\Builder\ScheduleReaderQueryBuilder;
use Alagunto\EzSchedules\Builder\ScheduleCreatorQueryBuilder;
use Alagunto\EzSchedules\Proxies\ScheduledRepetition;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait IsAScheduleItem
 * @package Alagunto\EzSchedules
 * @method static static from(\Carbon\Carbon|string $from)
 * @method static static to(\Carbon\Carbon|string $to)
 */
trait IsAScheduleItem
{
    public static function schedule() {
        return new ScheduleCreatorQueryBuilder(static::class);
    }

    public function newEloquentBuilder($query) {
        return new ScheduleReaderQueryBuilder($query);
    }

    /**
     * @return ScheduledRepetition|null
     */
    public function repetition() {
        if(is_null($this->repetition_id))
            return null;

        return new ScheduledRepetition(RepetitionStrategiesStorage::find(
            $this->repetition_id
        ));
    }

    public static function repetitions() {
        $query = RepetitionStrategiesStorage::query();

        $proxy = new ProxifiedRepetitionsReaderQueryBuilder($query->getQuery());
        $proxy->setModel($query->getModel());

        $proxy->where("item_model", static::class);

        return $proxy;
    }
}