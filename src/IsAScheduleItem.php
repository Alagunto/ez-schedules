<?php

namespace Alagunto\EzSchedules;

use Alagunto\EzSchedules\Builder\ScheduleReaderQueryBuilder;
use Alagunto\EzSchedules\Builder\ScheduleCreatorQueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait IsAScheduleItem
 * @package Alagunto\EzSchedules
 * @method static static from(\Carbon\Carbon $from)
 * @method static static to(\Carbon\Carbon $to)
 */
trait IsAScheduleItem
{
    public static function schedule() {
        return new ScheduleCreatorQueryBuilder(static::class);
    }

    public static function occurs() {
        return new ScheduleSingleItemCreatorQueryBuilder(static::class);
    }

    public function newEloquentBuilder($query)
    {
        return new ScheduleReaderQueryBuilder($query);
    }
}