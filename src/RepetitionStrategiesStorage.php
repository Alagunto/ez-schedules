<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 16/03/2018
 * Time: 19:20
 */

namespace Alagunto\EzSchedules;


use Alagunto\EzSchedules\JsonContainers\HasJsonContainers;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model {
    use HasJsonContainers;
}

class RepetitionStrategiesStorage extends Proxy
{
    protected $table = "repetition_rules";
    protected $guarded = [];

    protected $casts = [
        "put_params" => "json"
    ];

    protected $dates = [
        "starts_at",
        "ends_at"
    ];

    protected $json_containers = [
        "params" => RepetitionStrategiesStorageParamsContainer::class
    ];
}