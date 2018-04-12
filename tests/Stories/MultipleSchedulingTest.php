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
            ->from("now")
            ->at("12:30")
            ->endsAt("14:40")
            ->put(["user_id" => 13])
            ->save();

        ScheduleItem::schedule()
            ->each("Tuesday")
            ->from("now")
            ->at("15:30")
            ->endsAt("18:30")
            ->put(["user_id" => 18])
            ->save();

        $rules = ScheduleItem::repetitions()->get();
        $this->assertCount(2, $rules);

        $items = ScheduleItem::from("now")->to("next wednesday")->get();

        $this->assertCount(2, $items);

        // Asserting they are ok and not-crossed

        $item_one = $items->sortBy("starts_at")->first();
        $item_two = $items->sortByDesc("starts_at")->first();

        $this->assertEquals(13, $item_one->user_id);
        $this->assertEquals(18, $item_two->user_id);
        $this->assertEquals("12:30:00", Carbon::parse($item_one->starts_at)->toTimeString());
        $this->assertEquals("15:30:00", Carbon::parse($item_two->starts_at)->toTimeString());
        $this->assertEquals("14:40:00", Carbon::parse($item_one->ends_at)->toTimeString());
        $this->assertEquals("18:30:00", Carbon::parse($item_two->ends_at)->toTimeString());
    }
}