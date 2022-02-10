<?php

require_once './UnitClass.php';

class ArmyClass {

    // название армии
    private $armyName = '';

    // полководец
    private $commander = '';
    private $units = [];

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
        
        return $this;
    }

    public function deleteUnit(string $unitType){
        $index = $this->getUnitIndex($unitType);
        if (false !== $index)
            unset($this->units[$index]);
    }

    public function updateUnitCount(string $unitType, int $count = 0){
        $index = $this->getUnitIndex($unitType);
        if (false !== $index)
            $this->units[$index]->setCount($count);
    }

    private function getUnitByType(string $unitType){
        $index = $this->getUnitIndex($unitType);
        return (false !== $index) ? $this->units[$index] : false;
    }

    private function getUnitIndex(string $unitType){
        foreach($this->units as $key => $unit){
            if ( $unitType === $unit->getUnitType()){
                return $key;
            }
        }
        return false;
    }

    public function getUnitByIndex(int $index){
        return ($index >= 0 && $index < count($this->units))
            ? $this->units[$index]
            : false; 
    }

    public function getUnits()
    {   
        return $this->units;
    }

    public function getUnitsCount(){
        return count($this->units);
    }

    public function calcUnitDamage(string $unitType, array $conditions = []){
        $damage = 0;
        $unit = $this->getUnitByType($unitType);
        if (false !== $unit)
            {
                $bfCoeff = 1;
                foreach ($conditions as $condition){
                    $bfCoeff *= isset($this->battleFieldCoefficients[$unitType][$condition])
                        ? $this->battleFieldCoefficients[$unitType][$condition]
                        : 1;
                }
                $damage = $unit->getUnitDamage() * $bfCoeff;
            }
        return $damage;
    }

    public function calcArmyDamage(array $conditions){
        $result = 0;
        foreach($this->units as $unit){
            $result += $this->calcUnitDamage($unit->getUnitType(),$conditions);
        }

        return $result;
    }

    public function calcArmyHealth(){
        $result = 0;
        foreach($this->units as $u){
            $result += $u->getUnitHealth();
        }

        return $result;
    }

    public function getCommander(){
        return $this->commander;
    }

    public function getArmyName(){
        return $this->armyName;
    }

    public function addBattleFieldCoefficients(array $coefficients = [])
    {
        foreach($coefficients as $key=>$coefficient)
            $this->battleFieldCoefficients[$key] = $coefficient;
        return $this;
    }

    public function addBattleFieldCoefficientsForUnit(string $unit_type, array $coefficients)
    {
        $this->battleFieldCoefficients[$unit_type] = $coefficients;
        return $this;
    }

    public function __toString()
    {
        $result=[];
        foreach($this->units as $unit){
            $result[] = $unit->__toString();
        }
        return implode(', ',$result);
    }
}

