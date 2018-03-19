<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 06/03/2018
 * Time: 02:27
 */

namespace Alagunto\EzSchedules\Builder;

use Alagunto\EzSchedules\Contracts\RepetitionStrategy;
use Alagunto\EzSchedules\Eacher\EacherRepetitionStrategy;
use Alagunto\EzSchedules\RepetitionStrategiesStorage;
use Alagunto\EzSchedules\SchedulesManager;
use Alagunto\EzSchedules\Test\ScheduleItem;
use Mockery\Exception;

class ScheduleCreatorQueryBuilder
{
    protected $model_class;

    /** @var RepetitionStrategy */
    protected $rule;
    protected $put_closure;
    protected $put_params;
    protected $priority = 0;

    public function __construct($model_class) {
        $this->model_class = $model_class;
        $this->serializer = new \SuperClosure\Serializer(null, config("app.key"));

    }

    public function each($what) {
        $this->rule = new EacherRepetitionStrategy($this->model_class, $what);

        return $this;
    }

    public function repeat($strategy) {
        $this->rule = new $strategy();
        if(!$this->rule instanceof RepetitionStrategy)
            throw new Exception("You probably forgot to inherit your strategy from RepetitionStrategy. Please, do it :)");

        return $this;
    }

    public function withPriority($priority) {
        $this->failWithoutRule()->priority = $priority;

        return $this;
    }
    
    public function if(Callable $closure) {
        $this->failWithoutRule()->rule->addLimitation(
            $this->serializer->serialize($closure)
    );

        return $this;
    }

    public function andIf(Callable $closure) {
        $this->failWithoutRule()->if($closure);

        return $this;
    }

    public function from($from) {
        $this->failWithoutRule()->rule->setStartsAt($from);

        return $this;
    }

    public function to($to) {
        $this->failWithoutRule()->rule->setEndsAt($to);

        return $this;
    }

    public function put($items_or_closure) {
        $this->failWithoutRule();

        if(is_array($items_or_closure)) {
            $this->put_params = $items_or_closure;
        } else {
            $this->put_closure = $items_or_closure;
        }

        return $this;
    }

    public function save() {
        $this->failWithoutRule();

        $storage = new RepetitionStrategiesStorage();
        $storage->repetition_strategy = get_class($this->rule);
        $storage->item_model = $this->model_class;
        $storage->put_params = $this->put_params;
        $storage->priority = $this->priority;

        if($this->put_closure) {
           $storage->put_closure = $this->serializer->serialize($this->put_closure);
        }

        $this->rule->save($storage);

        return $storage->save();
    }

    private function failWithoutRule() {
        if(!$this->rule)
            throw new Exception("Wait, you have to set rule with 'repeat' or 'each' before doing that");

        return $this;
    }
}