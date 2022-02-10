<?php

interface UnitInterface
{

    public function __construct(string $unit_type, int $health, int $armour, int $damage, string $name='');

    public function getName(): string;
    public function getHealth(): int;
    public function getArmour(): int;
    public function getDamage(): int;
    public function getUnitType(): string;
    
}
