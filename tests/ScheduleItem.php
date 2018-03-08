<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 08/03/2018
 * Time: 17:57
 */

namespace Alagunto\EzSchedules\Test;

use Alagunto\EzSchedules\IsAScheduleItem;
use Illuminate\Database\Eloquent\Model;

class ScheduleItem extends Model
{
    use IsAScheduleItem;
}