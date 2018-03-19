<?php

namespace Alagunto\EzSchedules\JsonContainers;

trait HasJsonContainers
{
    protected $json_containers = [];
    protected $container_instances = [];
    public $is_observer_registered = false;

    public function getAttribute($key) {
        if (array_key_exists($key, $this->json_containers)) {
            if (isset($this->container_instances[$key])) {
                /** @var JsonSLOB $container */
                $container = $this->container_instances[$key];
                $container->touch();

                return $container;
            }

            // TODO: shift json container abstract class stuff to contract and check if the given object implements it

            /** @var JsonSLOB $container */
            $container = new $this->json_containers[$key]($this->attributes[$key] ?? null, $this, $key);
            $container->touchOriginal();

            $this->container_instances[$key] = $container;

            if (!$this->is_observer_registered) {
                static::registerModelEvent("saving", function () {
                    $this->touchAllJsonContainers();
                });

                $this->is_observer_registered = true;
            }

            return $this->container_instances[$key];
        }

        return parent::getAttribute($key);
    }

    public function toArray() {
        $result = [];
        foreach ($this->json_containers as $key => $instance_class) {
            $result[$key] = $this->$key->toArray();
        }

        return array_merge(parent::toArray(), $result);
    }

    public function touchAllJsonContainers() {
        /** @var JsonSLOB $instance */
        foreach ($this->container_instances as $instance) {
            $instance->touch();
        }

        return true; // Because we'll be a listener
    }

    public function getAttributeValue($key) {
        return parent::getAttributeValue($key);
    }

    public function setJsonContainerValue($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function setJsonContainerOriginalValue($key, $value) {
        $this->original[$key] = $value;
    }

    public function getDirty() {
        $this->touchAllJsonContainers();

        return parent::getDirty();
    }

    public function refresh() {
        /** @var JsonSLOB $instance */
        foreach ($this->container_instances as $instance) {
            $instance->disarm();
        }
        $this->container_instances = [];
        parent::refresh();
    }
}