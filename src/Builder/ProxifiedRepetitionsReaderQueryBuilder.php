<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 23/03/2018
 * Time: 20:26
 */

namespace Alagunto\EzSchedules\Builder;


use Alagunto\EzSchedules\Proxies\ScheduledRepetition;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ProxifiedRepetitionsReaderQueryBuilder extends Builder
{
    public function get($columns = ['*']) {
        $data = parent::get($columns);

        if(!$data)
            return $data;

        $answer = [];
        foreach($data as $model) {
            if($model instanceof RepetitionStrategiesStorage)
                $answer[] = new ScheduledRepetition($model);
            else
                $answer[] = $model;
        }

        return collect($answer);
    }
}