<?php

include './UnitInterface.php';

class UnitClass implements UnitInterface
{
    const UNIT_TYPE_INFANTRY = 'infantry';
    const UNIT_TYPE_ARCHERS = 'archers';
    const UNIT_TYPE_HORSEMEN = 'horsemen';

    const UNIT_TYPES = [
        self::UNIT_TYPE_INFANTRY,
        self::UNIT_TYPE_ARCHERS,
        self::UNIT_TYPE_HORSEMEN
    ];

    protected $health = 0;
    protected $armour = 0;
    protected $damage = 0;
    protected $unit_type = null;

    public function __construct(string $unit_type, int $health, int $armour, int $damage, string $name=''){
        $this->health = $health;
        $this->armour = $armour;
        $this->damage = $damage;
        $this->name = $name;
        $this->unit_type = in_array($unit_type,self::UNIT_TYPES) ?  $unit_type :  0;
 
    }
       
    public function getName(): string {
        return $this->name;
    }
    
    public function getHealth(): int {
        return $this->health;
    }

    public function getArmour(): int {
        return $this->armour;
    }

    public function getDamage(): int {
        return $this->damage;
    }
    
    public function getUnitType(): string {
        return $this->unit_type;
    } 

}

class Units extends UnitClass{
    private $count = 0;


    public function __construct(string $unit_type, int $health, int $armour, int $damage, string $name='')
    {
        parent::__construct($unit_type,$health,$armour,$damage,$name);
    }

    public function setCount(int $count){
        $this->count = $count;
    }

    public function getCount(){
        return $this->count;
    }

    public function getUnitHealth(){
        return ($this->health + $this->armour) * $this->count;
    }

    public function getUnitDamage(array $conditions = []){
        return $this->damage * $this->count;
    }

    public function getUnitArmour(){
        return $this->armour * $this->count;
    }

    public function __toString()
    {
        return $this->unit_type . "(" . $this->count . ")";
    }

    public function calcHealthToCount(int $healthCount){
        return ceil($healthCount / ($this->health + $this->armour));
    }
}