<?php
/**
 * Created by PhpStorm.
 * User: alagunto
 * Date: 05/04/2018
 * Time: 19:58
 */

namespace Alagunto\EzSchedules;


class RepeatedItemsPacker
{
    /** @var RepetitionStrategiesStorage */
    protected $storage;
    protected $items;

    public function __construct(RepetitionStrategiesStorage $storage, $generated_items) {
        $this->items = $generated_items;
        $this->storage = $storage;
    }

    public function packWith($packs) {
        foreach($this->items as $item) {
            // Transcendent items are just left as they are
            if($this->storage->params->core["transcendent"] ?? false)
                continue;

            foreach($packs as $pack) {
                // We do not dare to touch transcendent ones
                if($pack["storage"]->params->core["transcendent"] ?? false)
                    continue;

                foreach($pack["items"] as $potential_collider) {
                    if(!$this->collides($item, $potential_collider))
                        continue;

                    // Now we battle!
                    $our_priority = $this->storage->params->core["priority"] ?? 0;
                    $his_priority = $pack["storage"]->params->core["priority"] ?? 0;

                    $this->battle($item, $potential_collider, $our_priority, $his_priority);
                }
            }
        }

        $packs[] = [
            "items" => $this->items,
            "storage" => $this->storage
        ];

        return $packs;
    }

    private function collides($a, $b){
        return (
            min($a->ends_at ?? $a->starts_at, $b->ends_at ?? $b->starts_at)->greaterThan(
                max($a->starts_at, $b->starts_at)
            )
        );
    }

    private function battle($me, $him, $my_power, $his_power) {
        // Easter egg: you mom gay

        if($my_power >= $his_power) {
            // Because fuck you, I'm stronger
            if($me->starts_at > $him->starts_at) {
                $him->ends_at = $me->start_at;
            } else {
                $him->starts_at = $me->ends_at;
            }
        } else {
            // That's how much your mom is gay
            $this->battle($him, $me, $his_power, $my_power);
        }
    }
}