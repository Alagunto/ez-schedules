<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 16/03/2018
 * Time: 19:34
 */

namespace Alagunto\EzSchedules;


use Alagunto\EzSchedules\JsonContainers\JsonSLOB;

class RepetitionStrategiesStorageParamsContainer extends JsonSLOB
{
    public $public;
    public $core;

    protected function default() {
        return [
            "public" => new class {},
            "core" => new class {}
        ];
    }
}