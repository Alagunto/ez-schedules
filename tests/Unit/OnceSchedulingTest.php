<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 23/03/2018
 * Time: 21:26
 */

namespace Alagunto\EzSchedules\Test\Unit;

use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OnceSchedulingTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_we_can_schedule_once() {
        ScheduleItem::schedule()->once()->at(Carbon::now())->save();

        /** @var Collection $items */
        $items = ScheduleItem::from("now")->to("next year")->get();

        $this->assertCount(1, $items);

        /** @var ScheduleItem $item */
        $item = $items->first();

        $this->assertTrue(Carbon::parse($item->starts_at)->equalTo(Carbon::now()));
    }
}