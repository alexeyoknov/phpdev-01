<?php

namespace War\Battle;

use War\Unit\ArmyClass;

class LineBattle extends AbstractBattle
{
    protected array $duration = [];
    private int $maxUnitsCount = 0;
    private array $unitsBattleResult = [];
    private array $winsCount = [];

    public function init($battleFieldConditions = []):void
    {
        $this->battleFieldConditions = $battleFieldConditions;

        $this->armyBattleResult = [];
        $this->duration = [];
        $this->maxUnitsCount = 0;
        $this->unitsBattleResult = [];
        $this->winsCount = [0,0];

        foreach ($this->army as $key=>&$army) {
            $army->battleInit();
            $this->armyBattleResult[$key]['commander'] = $army->getCommander();
            $this->maxUnitsCount = max($this->maxUnitsCount, $army->getUnitsCount());

            foreach ($army->getUnits() as $k=>&$unit) {
                $this->armyBattleResult[$key]['damage'][$k] = $unit->getUnitDamage($battleFieldConditions);
                $this->armyBattleResult[$key]['health'][$k] = $unit->getUnitHealth();
            }

            $this->armyBattleResult[$key]['army_units_before'] = (string)$army;
        }
    }

    public function battleRun():void
    {
        $armyUnits1 = $this->army[0]->getUnits();
        $armyUnits2 = $this->army[1]->getUnits();

        for ((int) $i=0; $i<$this->maxUnitsCount; $i++) {
            // непосредственно само сражение

            if (!($this->armyBattleResult[0]['damage'][$i] === 0 && $this->armyBattleResult[1]['damage'][$i] === 0)) {
                while ($armyUnits1[$i]->getHealthInBattle() > 0 && $armyUnits2[$i]->getHealthInBattle() > 0) {
                    $armyUnits1[$i]->getEnemyDamage($this->armyBattleResult[1]['damage'][$i]);
                    $armyUnits2[$i]->getEnemyDamage($this->armyBattleResult[0]['damage'][$i]);
                    $this->duration[$i]++;
                }
            }

            if ($armyUnits1[$i]->getHealthInBattle()>$armyUnits2[$i]->getHealthInBattle()) {
                $this->winsCount[0]++;
            } else {
                $this->winsCount[1]++;
            }

            $this->unitsBattleResult[0][$i] = $this->battleResult($armyUnits1[$i]->getHealthInBattle(), $armyUnits2[$i]->getHealthInBattle());
            $this->unitsBattleResult[1][$i] = $this->battleResult($armyUnits2[$i]->getHealthInBattle(), $armyUnits1[$i]->getHealthInBattle());
        }

        $this->battleAftermath();
    }

    private function battleAftermath()
    {
        $this->findSurvivingUnits();

        $this->armyBattleResult[0]['Result'] = implode('/', $this->unitsBattleResult[0]) . ":<br>" . $this->battleResult($this->winsCount[0], $this->winsCount[1]);
        $this->armyBattleResult[1]['Result'] = implode('/', $this->unitsBattleResult[1]) . ":<br>" . $this->battleResult($this->winsCount[1], $this->winsCount[0]);

        $this->battleResult = [
            'BattleName' => 'Line By Line Battle',
            'Hits' => implode('/', $this->duration),
            'BattleFieldConditions' => implode(', ', $this->battleFieldConditions),
            'army' => $this->armyBattleResult
        ];
    }

    private function findSurvivingUnits()
    {
        foreach ($this->army as $k=>$army) {
            foreach ($army->getUnits() as $v=>$unit) {
                $army_health[$k][$v] = $unit->getHealthInBattle();
                $army_units[$k][$v] = (string)$unit;
            }
            $this->armyBattleResult[$k]['health'] = implode('/', $army_health[$k]);
            $this->armyBattleResult[$k]['armyUnits'] = implode(', ', $army_units[$k]);
        }
    }
}
