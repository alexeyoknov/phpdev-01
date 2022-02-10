<?php

require_once './BattleClass.php';

$bfCoefficients = [
    Units::UNIT_TYPE_INFANTRY => [
        BattleFieldConditions::BF_CONDITION_RAIN => 0.75,
        BattleFieldConditions::BF_CONDITION_ICE => 0.3
    ],
    Units::UNIT_TYPE_ARCHERS => [
        BattleFieldConditions::BF_CONDITION_RAIN => 0
    ],
    Units::UNIT_TYPE_HORSEMEN => [
        BattleFieldConditions::BF_CONDITION_ICE => 0.5,
        BattleFieldConditions::BF_CONDITION_RAIN => 0.9
    ]
];

$army1 = (new ArmyClass('Александр Ярославич'))
    ->addUnit(new Units(Units::UNIT_TYPE_INFANTRY,100,10,10),200)
    ->addUnit(new Units(Units::UNIT_TYPE_ARCHERS,100,5,20),30)
    ->addUnit(new Units(Units::UNIT_TYPE_HORSEMEN,300,30,30),15)
    ->addBattleFieldCoefficients($bfCoefficients); // могут быть разные коэффициенты у каждой армии (лучше экипировка и прочее) 

$army2 = (new ArmyClass('Ульф Фасе'))
    ->addUnit(new Units(Units::UNIT_TYPE_INFANTRY,100,10,10),90)
    ->addUnit(new Units(Units::UNIT_TYPE_HORSEMEN,300,30,30),25)
    ->addUnit(new Units(Units::UNIT_TYPE_ARCHERS,100,5,20),65)
    ->addBattleFieldCoefficients($bfCoefficients);

$battle = new Battle($army1,$army2);

$battles_conditions = [
    [],
    BattleFieldConditions::getAvailableConditions(),
    [BattleFieldConditions::BF_CONDITION_ICE],
    [BattleFieldConditions::BF_CONDITION_RAIN]
];

foreach($battles_conditions as $battle_condition){
    $battle->runCommonBattle($battle_condition);
    $battle->runLineBattle($battle_condition);
}
