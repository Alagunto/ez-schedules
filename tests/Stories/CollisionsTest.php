<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 12/04/2018
 * Time: 03:44
 */

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class CollisionsTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_items_can_collide() {
        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("14:00")->endsAt("16:00")->withPriority(1)->save();

        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("15:00")->endsAt("17:00")->save();

        // Since first rule has more priority and collides with the second one,
        // it will stand as 14:00 <-> 16:00, and the second will be
        // 16:00 <-> 17:00

        $items = ScheduleItem::from("now")->to("next wednesday")->get();
        $first = $items->sortBy("starts_at")->first();
        $second = $items->sortByDesc("starts_at")->first();

        $this->assertEquals("14:00:00", Carbon::parse($first->starts_at)->toTimeString());
        $this->assertEquals("16:00:00", Carbon::parse($first->ends_at)->toTimeString());

        $this->assertEquals("16:00:00", Carbon::parse($second->starts_at)->toTimeString());
        $this->assertEquals("17:00:00", Carbon::parse($second->ends_at)->toTimeString());
    }

    /**
     * @throws \Exception
     */
    public function test_colliding_items_can_be_independent_via_collide_by() {
        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("14:00")->endsAt("16:00")->withPriority(1)
            ->put(["user_id" => 14])
            ->collideBy(["user_id"])
            ->save();

        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("15:00")->endsAt("17:00")
            ->put(["user_id" => 18])
            ->collideBy(["user_id"])
            ->save();

        // Now, they have collideBy that says "only those who have same user_id are colliding"
        // If someone has empty collide by, he collides at everything (!)
        // If you want no collisions, consider using transcendent()

        $items = ScheduleItem::from("now")->to("next wednesday")->get();
        $first = $items->sortBy("starts_at")->first();
        $second = $items->sortByDesc("starts_at")->first();

        $this->assertEquals("14:00:00", Carbon::parse($first->starts_at)->toTimeString());
        $this->assertEquals("16:00:00", Carbon::parse($first->ends_at)->toTimeString());

        $this->assertEquals("15:00:00", Carbon::parse($second->starts_at)->toTimeString());
        $this->assertEquals("17:00:00", Carbon::parse($second->ends_at)->toTimeString());
    }

    /**
     * @throws \Exception
     */
    public function test_colliding_items_collide_with_provided_equal_colliders() {
        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("14:00")->endsAt("16:00")
            ->put(["user_id" => 14])
            ->collideBy(["user_id"])
            ->withPriority(1)
            ->save();

        ScheduleItem::schedule()->each("Monday")->from("now")
            ->at("15:00")->endsAt("17:00")
            ->put(["user_id" => 14])
            ->collideBy(["user_id"])
            ->save();


        $items = ScheduleItem::from("now")->to("next wednesday")->get();
        $first = $items->sortBy("starts_at")->first();
        $second = $items->sortByDesc("starts_at")->first();

        $this->assertEquals("14:00:00", Carbon::parse($first->starts_at)->toTimeString());
        $this->assertEquals("16:00:00", Carbon::parse($first->ends_at)->toTimeString());

        $this->assertEquals("16:00:00", Carbon::parse($second->starts_at)->toTimeString());
        $this->assertEquals("17:00:00", Carbon::parse($second->ends_at)->toTimeString());
    }
}