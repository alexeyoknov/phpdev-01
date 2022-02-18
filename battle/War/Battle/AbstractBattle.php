<?php

namespace War\Battle;

use War\Unit\ArmyClass;

abstract class AbstractBattle
{
    const BATTLE_RESULT = [
        0 => 'LOOSER',
        1 => 'WINNER'
    ];

    protected $army = [];
    protected $battleFieldConditions = [];

    protected array $armyBattleResult = [];
    protected array $battleResult = [];

    abstract public function init($battleFieldConditions = []): void;
    abstract public function battleRun(): void;

    public function getVars()
    {
        $vars = [];
        foreach ($this->battleResult as $key=>$val) {
            if ('army' !== $key) {
                $vars["%%$key%%"] = $val;
            } else {
                for ($index=0; $index<count($val); $index++) {
                    foreach ($val[$index] as $k=>$v) {
                        $vl = ("health" === $k && is_array($v))
                            ? implode('/', $v)
                            : $v;

                        $vars["%%$k" . ($index+1) . "%%"] = $vl;
                    }
                }
            }
        }
        return $vars;
    }

    public function showResult()
    {
        echo strtr(file_get_contents("./War/Battle/table.tpl"), $this->getVars());
    }

    public function __construct(ArmyClass $army1, ArmyClass $army2, array $battleFieldConditions = [])
    {
        $this->army[] = $army1;
        $this->army[] = $army2;
        $this->battleFieldConditions = $battleFieldConditions;
    }

    protected function battle(&$startHealth1, &$startHealth2, &$damage1, &$damage2)
    {
        $duration = 0;
        if ($damage1 === 0 && $damage2 === 0) {
            return 0;
        }

        while ($startHealth1 > 0 && $startHealth2 > 0) {
            $startHealth1 -= $damage2;
            $startHealth2 -= $damage1;
            $duration++;
        }
        return $duration;
    }

    protected function battleResult(int $health1, int $health2)
    {
        return self::BATTLE_RESULT[$health1>$health2];
    }
}
