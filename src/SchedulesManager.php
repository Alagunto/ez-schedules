<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 08/03/2018
 * Time: 18:12
 */

namespace Alagunto\EzSchedules;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class SchedulesManager
{
    /**
     * @param string $class
     * @param $from
     * @param $to
     * @throws \Exception
     */
    public function resolveScheduleItemsForModel(string $class, $from, $to) {
        $resulting_items = [];

        $storages = RepetitionStrategiesStorage::query()
            ->whereNested(function(Builder $query) use ($from, $to) {
                $query->where("starts_at", "<=", $to)
                    ->orWhere("starts_at", null);
            })
            ->whereNested(function(Builder $query) use ($from, $to) {
                $query->where("ends_at", ">=", $from)
                    ->orWhere("ends_at", null);
            })
            ->where("item_model", $class)
            ->orderBy("priority")
            ->get();

        foreach($storages as $storage) {
            /** @var RepetitionStrategy $repetition_strategy */
            $repeater = new RepeatedItemsGenerator($storage);
            $resulting_items[] = $repeater->generate($from, $to);
        }

        return $resulting_items;
    }
}