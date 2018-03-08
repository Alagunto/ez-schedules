<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 08/03/2018
 * Time: 18:12
 */

namespace Alagunto\EzSchedules;

use Illuminate\Database\Eloquent\Model;

class SchedulesManager
{
    /**
     * @param string $class
     * @param $from
     * @param $to
     * @throws \Exception
     */
    public function resolveScheduleItemsForModel(string $class, $from, $to) {
        dump("from $from to $to");
    }
}