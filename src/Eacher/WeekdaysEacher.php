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
            $item = mb_strtolower($item);
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
    public function __construct($weekdays, $model, $time) {
        if(is_string($weekdays))
            $this->weekdays = [mb_strtolower($weekdays)];
        else
            $this->weekdays = $weekdays;
        
        $this->model = $model;
        $this->time = $time;
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
                $model->starts_at = clone $current_day;

                $time = Carbon::parse($this->time);

                /** @var Carbon $model->starts_at */
                $model->starts_at->hour($time->hour);
                $model->starts_at->minute($time->minute);
                $model->starts_at->second($time->second);

                $generated_items[] = $model;

                dump('buep', $current_day);
            }

            $current_day->addDay();
        }

        return $generated_items;
    }
}