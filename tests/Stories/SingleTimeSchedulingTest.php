<?php

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Alagunto\EzSchedules\SimpleScheduleItem;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class SingleTimeSchedulingTest extends TestCase
{
    public function test_i_can_schedule_some_class_for_one_time() {
        ScheduleItem::schedule()->each("Monday")->put(function() {
            return [
                "user_id" => 13
            ];
        });

        dump(ScheduleItem::from(Carbon::now())->to(Carbon::now()->addMonth())->get()->toArray());
    }
}