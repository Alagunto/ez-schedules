<?php

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Alagunto\EzSchedules\SimpleScheduleItem;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class SingleTimeSchedulingTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_i_can_schedule_some_class_for_one_time() {
//        ScheduleItem::schedule()->each("Monday")->put(function() {
//            return [
//                "user_id" => 13
//            ];
//        })->at("11:00")->save();

        ScheduleItem::schedule()
            ->once()
            ->at(Carbon::now())
            ->put([
                "user_id" => 13
            ])
            ->save();

        dump(
            ScheduleItem::from(Carbon::now())->to(Carbon::now()->addMonth())->get()->toArray()
        );
    }
}