<?php

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\SimpleScheduleItem;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class SingleTimeSchedulingTest extends TestCase
{
    public function test_i_can_schedule_some_class_for_one_time() {
        dump(ScheduleItem::from(Carbon::now())->to(Carbon::tomorrow())->get());
    }
}