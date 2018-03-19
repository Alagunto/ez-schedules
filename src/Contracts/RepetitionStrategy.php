<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 16/03/2018
 * Time: 19:15
 */

namespace Alagunto\EzSchedules\Contracts;

use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

abstract class RepetitionStrategy
{
    protected $limitations = [];
    protected $starts_at = null;
    protected $ends_at = null;

    /**
     * Generates & provides ScheduleItems for a given time range based on this strategy
     */
    public abstract function provideFor($classname, Carbon $from, Carbon $to);

    public function addLimitation(Callable $closure) {
        $this->limitations[] = $closure;
    }

    public function setLimitations(array $limitations)  {
        $this->limitations = $limitations;
    }

    public function setStartsAt(Carbon $from) {
        $this->starts_at = $from;
    }

    public function setEndsAt(Carbon $to) {
        $this->ends_at = $to;
    }

    public function save(RepetitionStrategiesStorage $storage) {
        $storage->starts_at = $this->starts_at;
        $storage->ends_at = $this->ends_at;

        $storage->params->core->limitations = $this->limitations;
    }
}