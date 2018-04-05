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

        $packs = [];

        foreach($storages as $storage) {
            /** @var RepetitionStrategy $repetition_strategy */
            $repeater = new RepeatedItemsGenerator($storage);
            $generated_items = $repeater->generate($from, $to);

            /** @var Model $item */
            foreach($generated_items as $item) {
                $item->repetition_id = $storage->id;
            }

            $items_packer = new RepeatedItemsPacker($storage, $generated_items);
            $packs = $items_packer->packWith($packs);
        }

        $resulting_items = [];

        foreach($packs as $pack) {
            foreach($pack["items"] as $item) {
                $item->save();
                $resulting_items[] = $item;
            }
        }

        return $resulting_items;
    }
}