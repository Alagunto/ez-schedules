<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 19/03/2018
 * Time: 15:27
 */

namespace Alagunto\EzSchedules\Eacher;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WeekdaysEacher
{
    protected $model;

    public static function suitable($value) {
        if(is_string($value))
            $value = [$value];

        if(!is_array($value))
            return false;

        foreach($value as $item) {
            if(!in_array($item, self::weekdays()))
                return false;
        }

        if(empty($item))
            return false;

        return true;
    }

    protected static function weekdays() {
        return ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
    }

    protected $weekdays = [];

    /**
     * WeekdaysEacher constructor.
     * @param array $weekdays
     * @param null $integer_to_days_map
     * @throws \Exception
     */
    public function __construct($weekdays, $model) {
        if(is_string($weekdays))
            $this->weekdays = [$weekdays];
        else
            $this->weekdays = $weekdays;
        
        $this->model = $model;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return array
     */
    public function provide(Carbon $from, Carbon $to) {
        $generated_items = [];

        $current_day = $from->copy()->setTime(0, 0, 0);

        while($current_day < $to) {
            $weekday = mb_strtolower($current_day->format('l'));
            if(in_array($weekday, $this->weekdays)) {
                /** @var Model $model */
                $model = new $this->model();
                $model->starts_at = $weekday;
                $generated_items[] = $model;
            }
        }

        return [];
    }
}