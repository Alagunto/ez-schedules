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

    public function __construct($class, $each_what) {
        $this->each_what = $each_what;
    }

    public function provideFor($classname, Carbon $from, Carbon $to) {
    }

    public function save(RepetitionStrategiesStorage $storage) {
        parent::save($storage);
        $storage->params->public->each_what = $this->each_what;
    }
}