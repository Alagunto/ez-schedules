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
        // Todo: lower the fucking cyclomatic complexity of this bullshit cmon

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

                    // Colliders are keys on schedule items which must be equal
                    // or give true on a given function for schedule items to collide.
                    // Empty colliders items doesn't mean it is transcendent, but it means
                    // that in any case those items will collide with others
                    $his_colliders = collect($pack["storage"]->params->core["colliders"] ?? []);
                    $my_colliders = collect($this->storage->params->core["colliders"] ?? []);

                    $we_fight_if = empty($his_colliders) || empty($my_colliders)
                        || $this->collidersMatch($item, $potential_collider, $my_colliders)
                        || $this->collidersMatch($potential_collider, $item, $his_colliders);


                    if(!$we_fight_if)
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

    private function collidersMatch($me, $him, $my_colliders) {
        if(!empty($my_colliders)) {
            foreach($my_colliders as $collider) {
                $function = function($a, $b) {
                    return $a == $b;
                };

                if(is_array($collider)) {
                    $key = $collider[0];
                    $function = $collider[1];
                } else {
                    $key = $collider;
                }

                $result = $function($me->{$key}, $him->{$key});

                if(!$result)
                    return false;
            }
        };

        return true;
    }

    private function collides($a, $b) {
        return (
            min($a->ends_at ?? $a->starts_at, $b->ends_at ?? $b->starts_at)->gte(
                max($a->starts_at, $b->starts_at)
            )
        );
    }

    private function battle($me, $him, $my_power, $his_power) {
        // Easter egg: you mom gay

        if($my_power >= $his_power) {
            // Because fuck you, I'm stronger
            if($me->starts_at > $him->starts_at) {
                $him->ends_at = $me->starts_at;
            } else {
                $him->starts_at = $me->ends_at ?? $me->starts_at;
            }
        } else {
            // That's how much your mom is gay
            $this->battle($him, $me, $his_power, $my_power);
        }
    }
}