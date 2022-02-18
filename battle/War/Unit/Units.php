<?php

namespace War\Unit;

class Units extends UnitClass
{
    private int $count = 0;
    private int $healthInBattle = 0;
    private array $battleFieldCoefficients = [];

    public function __construct(string $unit_type, int $health, int $armour, int $damage, string $name='')
    {
        parent::__construct($unit_type, $health, $armour, $damage, $name);
        $this->battleInit();
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getUnitHealth()
    {
        return ($this->health + $this->armour) * $this->count;
    }

    public function getUnitDamage(array $conditions = [])
    {
        $bfCoefficient = 1;
        foreach ($conditions as $condition) {
            if (isset($this->battleFieldCoefficients[$condition])) {
                $bfCoefficient *= $this->battleFieldCoefficients[$condition];
            }
        }
        return $this->damage * $this->count * $bfCoefficient;
    }

    public function getUnitArmour()
    {
        return $this->armour * $this->count;
    }

    public function __toString()
    {
        return $this->unit_type . "(" . $this->calcHealthToCount($this->healthInBattle) . ")";
    }

    public function calcHealthToCount(int $healthCount)
    {
        return ceil($healthCount / ($this->health + $this->armour));
    }

    public function battleInit()
    {
        $this->healthInBattle = $this->getUnitHealth();
    }

    public function getHealthInBattle()
    {
        return $this->healthInBattle;
    }

    public function getEnemyDamage(int $damage)
    {
        $this->healthInBattle -= $damage;
    }

    public function setBattleFieldCoefficient(string $bfConditionType, float $value)
    {
        $this->battleFieldCoefficients[$bfConditionType] = $value;
    }
}
