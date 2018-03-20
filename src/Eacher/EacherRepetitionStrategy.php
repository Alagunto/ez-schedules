<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 16/03/2018
 * Time: 19:14
 */

namespace Alagunto\EzSchedules\Eacher;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Carbon\Carbon;

class EacherRepetitionStrategy extends RepetitionStrategy
{
    protected $each_what;

    public function setEach($each_what) {
        $this->each_what = $each_what;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     * @throws \Exception
     */
    public function provide($from, $to) {
        if(WeekdaysEacher::suitable($this->each_what))
            return (new WeekdaysEacher($this->each_what, $this->schedule_items_model, $this->time))->provide($from, $to);

        throw new \Exception("No suitable eacher was found for your values");
    }

    public function save(RepetitionStrategiesStorage $storage) {
        $storage->params->public->each_what = $this->each_what;
        parent::save($storage);
    }

    public function restoreFromStorage(RepetitionStrategiesStorage $storage) {
        $this->each_what = $storage->params->public["each_what"] ?? null;
        parent::restoreFromStorage($storage);
    }
}