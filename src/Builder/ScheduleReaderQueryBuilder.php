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
use Carbon\Carbon;

class ScheduleReaderQueryBuilder extends \Illuminate\Database\Eloquent\Builder
{
    protected $from = null;
    protected $to = null;

    /**
     * @param string|Carbon $from
     * @return $this
     * @throws \Exception
     */
    public function from($from) {
        if(is_string($from))
            $from = Carbon::parse($from);

        if(!($from instanceof Carbon))
            throw new \Exception("Cannot parse the 'from' argument");

        $this->from = $from;
        return $this;
    }

    /**
     * @param string|Carbon $to
     * @return $this
     * @throws \Exception
     */
    public function to($to) {
        if(is_string($to))
            $to = Carbon::parse($to);

        if(!($to instanceof Carbon))
            throw new \Exception("Cannot parse the 'to' argument");


        $this->to = $to;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function render() {
        $this->resolveItems();

        return true;
    }

    /**
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws \Exception
     */
    public function get($columns = ['*']) {
        if(is_null($this->from) || is_null($this->to))
            throw new \Exception("Please, specify from and to. Otherwise, any repetition will cause infinite items generation =)");
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