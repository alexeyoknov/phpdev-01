<?php

namespace War\Unit;

class UnitClass implements UnitInterface
{
    public const UNIT_TYPE_INFANTRY = 'infantry';
    public const UNIT_TYPE_ARCHERS = 'archers';
    public const UNIT_TYPE_HORSEMEN = 'horsemen';

    public const UNIT_TYPES = [
        self::UNIT_TYPE_INFANTRY,
        self::UNIT_TYPE_ARCHERS,
        self::UNIT_TYPE_HORSEMEN
    ];

    protected $health = 0;
    protected $armour = 0;
    protected $damage = 0;
    protected $unit_type = null;

    public function __construct(string $unit_type, int $health, int $armour, int $damage, string $name='')
    {
        $this->health = $health;
        $this->armour = $armour;
        $this->damage = $damage;
        $this->name = $name;
        $this->unit_type = in_array($unit_type, self::UNIT_TYPES) ?  $unit_type :  0;
    }
       
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function setHealth(int $health)
    {
        $this->health = $health;
    }


    public function getArmour(): int
    {
        return $this->armour;
    }

    public function setArmour(int $armour)
    {
        $this->armour = $armour;
    }

    public function getDamage(): int
    {
        return $this->damage;
    }
    
    public function setDamage(int $damage)
    {
        $this->damage = $damage;
    }

    public function getUnitType(): string
    {
        return $this->unit_type;
    }
}
