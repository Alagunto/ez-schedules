<?php

namespace Alagunto\EzSchedules;
use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Carbon\Carbon;

/**
 * Class RepeatedItemsGenerator
 * Generates items in a given time range defined by the repetition strategy and
 * it's params stored in the storage container
 * @package Alagunto\EzSchedules
 */
class RepeatedItemsGenerator
{
    public function __construct(RepetitionStrategiesStorage $storage) {
        $this->storage = $storage;
    }

    public function generate(Carbon $from, Carbon $to) {
        // Create an instance of strategy
        /** @var RepetitionStrategy $strategy */
        $strategy = new $this->storage["repetition_strategy"];

        // Feed the strategy the container
        $strategy->restoreFromStorage($this->storage);

        $generated_items = $strategy->provide($from, $to);

        return $generated_items;
    }
}