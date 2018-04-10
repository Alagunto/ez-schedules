<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 10/04/2018
 * Time: 22:04
 */

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class MultipleSchedulingTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_we_can_do_two_different_schedules() {
        ScheduleItem::schedule()
            ->each("Tuesday")
            ->at("12:30")
            ->to("14:40")
            ->put(["user_id", 13])
            ->save();

        ScheduleItem::schedule()
            ->each("Tuesday")
            ->at("15:30")
            ->to("18:30")
            ->put(["user_id", 13])
            ->save();

        $rules = ScheduleItem::repetitions()->get();
        $this->assertCount(2, $rules);

        $items = ScheduleItem::from("now")->to("next wednesday")->get();

        $this->assertCount(2, $items);

        // Asserting they are ok and not-crossed
    }
}