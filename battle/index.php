<?php

spl_autoload_register(function ($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }
    return false;
});

use War\Unit\Units;
use War\Unit\ArmyClass;
use War\Battle\CommonBattle;
use War\Battle\LineBattle;
use War\Battle\BattleFieldConditions;

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
    ->addUnit(new Units(Units::UNIT_TYPE_INFANTRY, 100, 10, 10), 200)
    ->addUnit(new Units(Units::UNIT_TYPE_ARCHERS, 100, 5, 20), 30)
    ->addUnit(new Units(Units::UNIT_TYPE_HORSEMEN, 300, 30, 30), 15)
    ->addBattleFieldCoefficients($bfCoefficients); // могут быть разные коэффициенты у каждой армии (лучше экипировка и прочее)

$army2 = (new ArmyClass('Ульф Фасе'))
    ->addUnit(new Units(Units::UNIT_TYPE_INFANTRY, 100, 10, 10), 90)
    ->addUnit(new Units(Units::UNIT_TYPE_HORSEMEN, 300, 30, 30), 25)
    ->addUnit(new Units(Units::UNIT_TYPE_ARCHERS, 100, 5, 20), 65)
    ->addBattleFieldCoefficients($bfCoefficients);

$battleCommon = new CommonBattle($army1, $army2);
$battleLine = new LineBattle($army1, $army2);

$battles_conditions = [
    [],
    BattleFieldConditions::getAvailableConditions(),
    [BattleFieldConditions::BF_CONDITION_ICE],
    [BattleFieldConditions::BF_CONDITION_RAIN]
];

foreach ($battles_conditions as $battle_condition) {
    $battleCommon->init($battle_condition);
    $battleCommon->battleRun();
    $battleCommon->showResult();

    $battleLine->init($battle_condition);
    $battleLine->battleRun();
    $battleLine->showResult();
}
