<?php

namespace Alagunto\EzSchedules\Test\Stories;

use Alagunto\EzSchedules\Eacher\EacherRepetitionStrategy;
use Alagunto\EzSchedules\Proxies\ScheduledRepetition;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Alagunto\EzSchedules\Test\TestCase;
use Carbon\Carbon;

class SingleTimeSchedulingTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_i_can_schedule_some_class_for_one_time() {
        ScheduleItem::schedule()
            ->each("Monday")
            ->put(function() {
                return [
                    "user_id" => 13
                ];
            })->at("11:00")->save();


        ScheduleItem::from(Carbon::now())->to(Carbon::now()->addYear())->get()->toArray();
    }

    /**
     * @throws \Exception
     */
    public function test_one_repetition_gives_info_about_it() {
        ScheduleItem::schedule()->each("Monday")
            ->from("now")
            ->to("tomorrow")
            ->put([
                "user_id" => 2
            ])
            ->at("11:00")->save();

        $this->assertEquals(1, ScheduleItem::repetitions()->get()->count());

        /** @var ScheduledRepetition $repetition */
        $repetition = ScheduleItem::repetitions()->get()->first();

        $this->assertEquals("11:00", $repetition->getTime());

        $this->assertTrue($repetition->getStartsAt()->equalTo(
            Carbon::parse("first monday of january 1990")
        ));

        $this->assertTrue($repetition->getEndsAt()->equalTo(
            Carbon::parse("first tuesday of january 1990")
        ));

        $this->assertEquals(ScheduleItem::class, $repetition->getScheduleItemClassname());

        $this->assertEquals(EacherRepetitionStrategy::class, $repetition->getRepetitionStrategyClassname());
    }
}