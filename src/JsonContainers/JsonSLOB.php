<?php

namespace Alagunto\EzSchedules\JsonContainers;

/*
 * This guy is stored in database as a Serialized Large Object.
 * That's much better than storing a simple json since sometimes this json desires logic
 */
use ReflectionObject;
use ReflectionProperty;

abstract class JsonSLOB
{
    protected $container = null;

    protected $model;
    protected $key;

    public function __construct($string, $model = null, $key = null) {
        $this->loadFromString($string);

        $this->model = $model;
        $this->key = $key ?? studly_case(class_basename(static::class));

        $this->build();
    }

    public function getModel() {
        return $this->model;
    }

    public function getKey() {
        return $this->key;
    }

    public function toArray() {
        return $this->container;
    }

    public function touch() {
        if (is_null($this->model)) {
            return false;
        }

        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->name;
            if ($name[0] == "_" && in_array(substr($name, 1), $this->reservedContainerFields())) {
                $name = substr($name, 1);
            }

            $this->container[$name] = $this->{$property->name};
        }

        $this->model->setJsonContainerValue($this->key, json_encode($this->container));

        return true;
    }

    public function touchOriginal() {
        $this->touch();
        $this->model->setJsonContainerOriginalValue($this->key, json_encode($this->container));
    }

    protected function build() {
        foreach ($this->container as $key => $value) {
            $prepared_key = $this->prepareContainerKey($key);

            $method_name = "init" . studly_case($key);
            if (method_exists($this, $method_name)) {
                $this->{$method_name}($value);
            } else {
                $this->$prepared_key = $value;
            }
        }
    }

    protected function reservedContainerFields() {
        return ["container", "model", "key"];
    }

    protected function prepareContainerKey($key) {
        if (in_array($key, $this->reservedContainerFields())) {
            return "_$key";
        }

        return $key;
    }

    protected function loadFromString($string) {
        if (is_null($string) || empty($string)) {
            $this->container = $this->default();
        } else {
            $this->container = json_decode($string, true);
        }
    }

    public function stored($value, $default = null) {
        return $this->container[$value] ?? $default;
    }

    protected function default() {
        return null;
    }

    public function disarm() {
        $this->model = null;
    }
}
    