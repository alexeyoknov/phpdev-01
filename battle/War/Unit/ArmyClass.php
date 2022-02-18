<?php

namespace War\Unit;

//require_once './UnitClass.php';

class ArmyClass
{

    // название армии
    private $armyName = '';

    // полководец
    private $commander = '';

    //войска
    private $units = [];

    private int $armyHealth = 0;
    private int $armyDamage = 0;

    protected int $armyHealthInBattle = 0;

    // могут быть разные коэффициенты у каждой армии (лучше экипировка и прочее)
    private $battleFieldCoefficients = [];

    public function __construct(string $commander, string $armyName = '')
    {
        $this->armyName = $armyName;
        $this->commander = $commander;
    }

    public function addUnit(Units $unit, int $count)
    {
        $type = $unit->getUnitType();
        $unit->setCount($count);
        $this->units[] = $unit;
        $this->battleFieldCoefficients[$type]=[];
        $this->armyHealth += $unit->getUnitHealth();
        $this->armyDamage += $unit->getUnitDamage();
        
        return $this;
    }

    public function deleteUnit(string $unitType)
    {
        $index = $this->getUnitIndex($unitType);
        if (false !== $index) {
            $this->armyHealth -= $this->units[$index]->getUnitHealth();
            $this->armyDamage -= $this->units[$index]->getUnitDamage();
            unset($this->units[$index]);
        }
    }

    public function updateUnitCount(string $unitType, int $count = 0)
    {
        $index = $this->getUnitIndex($unitType);
        if (false !== $index) {
            $this->units[$index]->setCount($count);
            $this->armyDamage = $this->calcArmyDamage();
        }
    }

    private function getUnitIndex(string $unitType)
    {
        foreach ($this->units as $key => $unit) {
            if ($unitType === $unit->getUnitType()) {
                return $key;
            }
        }
        return false;
    }

    public function getUnitByIndex(int $index)
    {
        return ($index >= 0 && $index < count($this->units))
            ? $this->units[$index]
            : false;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function getUnitsCount()
    {
        return count($this->units);
    }

    public function calcArmyDamage(array $conditions=[])
    {
        $result = 0;
        foreach ($this->units as $unit) {
            $result += $unit->getUnitDamage($conditions);
        }

        return $result;
    }

    public function calcArmyHealth()
    {
        $result = 0;
        foreach ($this->units as $u) {
            $result += $u->getUnitHealth();
        }

        return $result;
    }

    public function getCommander()
    {
        return $this->commander;
    }

    public function getArmyName()
    {
        return $this->armyName;
    }

    public function addBattleFieldCoefficients(array $coefficients = [])
    {
        foreach ($coefficients as $unit_type=>$unit_coefficients) {
            $this->addBattleFieldCoefficientsForUnit($unit_type, $unit_coefficients);
        }
        return $this;
    }

    public function addBattleFieldCoefficientsForUnit(string $unit_type, array $coefficients)
    {
        foreach ($coefficients as $key=>$coefficient) {
            $index = $this->getUnitIndex($unit_type);
            if ($index !== false) {
                $this->units[$index]->setBattleFieldCoefficient($key, $coefficient);
            }
        }

        return $this;
    }

    public function getArmyHealthInBattle()
    {
        return $this->armyHealthInBattle;
    }

    public function getEnemyDamage(int $damage)
    {
        $this->armyHealthInBattle -= $damage;
    }

    public function __toString()
    {
        $result=[];
        foreach ($this->units as $unit) {
            $result[] = (string)$unit;
        }
        return implode(', ', $result);
    }

    public function battleInit()
    {
        $this->armyHealthInBattle = $this->calcArmyHealth();
        foreach ($this->units as &$unit) {
            $unit->battleInit();
        }
    }
}
