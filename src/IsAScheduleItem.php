<?php

namespace Alagunto\EzSchedules;

use Alagunto\EzSchedules\Builder\ScheduleItemQueryBuilder;
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

    }

    public function newEloquentBuilder($query)
    {
        return new ScheduleItemQueryBuilder($query);
    }
}