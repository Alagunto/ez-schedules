<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 06/03/2018
 * Time: 02:27
 */

namespace Alagunto\EzSchedules\Builder;

use Alagunto\EzSchedules\SchedulesManager;
use Alagunto\EzSchedules\Test\ScheduleItem;

class ScheduleItemQueryBuilder extends \Illuminate\Database\Eloquent\Builder
{
    protected $from = null;
    protected $to = null;

    public function from($from) {
        $this->from = $from;

        return $this;
    }

    public function to($to) {
        $this->to = $to;

        return $this;
    }

    public function get($columns = ['*']) {
        $this->resolveItems();
        return parent::get($columns);
    }

    /**
     * @throws \Exception
     */
    public function resolveItems() {
        if(!$this->model)
            throw new \Exception("EzSchedules can't resolve model on which we operate :(");

        /** @var SchedulesManager $manager */
        $manager = app(SchedulesManager::class);
        $manager->resolveScheduleItemsForModel(get_class($this->model), $this->from, $this->to);
    }
}