<?php

namespace War\Battle;

class CommonBattle extends AbstractBattle
{
    protected int $duration = 0;

    public function init($battleFieldConditions = []):void
    {
        $this->battleFieldConditions = $battleFieldConditions;

        $this->armyBattleResult = [];
        $this->duration = 0;

        foreach ($this->army as $key=>&$army) {
            $army->battleInit();
            $this->armyBattleResult[$key]['commander'] = $army->getCommander();
            $this->armyBattleResult[$key]['damage'] = $army->calcArmyDamage($battleFieldConditions);
            $this->armyBattleResult[$key]['health'] = $army->calcArmyHealth();
            $this->armyBattleResult[$key]['army_units_before'] = $army;
        }
    }

    public function battleRun():void
    {
        if (!($this->armyBattleResult[0]['damage'] === 0 && $this->armyBattleResult[1]['damage'] === 0)) {
            while ($this->army[0]->getArmyHealthInBattle() > 0 && $this->army[1]->getArmyHealthInBattle() > 0) {
                $this->army[0]->getEnemyDamage($this->armyBattleResult[1]['damage']);
                $this->army[1]->getEnemyDamage($this->armyBattleResult[0]['damage']);
                $this->duration++;
            }
        }
        $this->battleAftermath();
    }

    private function battleAftermath()
    {
        $this->findSurvivingUnits();

        $this->battleResult = [
            'BattleName' => 'Common Battle',
            'Hits' => $this->duration,
            'BattleFieldConditions' => implode(', ', $this->battleFieldConditions),
            'army' => $this->armyBattleResult
        ];
    }

    private function findSurvivingUnits()
    {
        $armyFinishHealth = [
            $this->army[0]->getArmyHealthInBattle(),
            $this->army[1]->getArmyHealthInBattle()
        ];

        foreach ($this->army as $key => &$army) {
            $startUnitIndex = $army->getUnitsCount()-1;
            $startHealth = $army->calcArmyHealth();
            $finishHealth = $armyFinishHealth[$key];

            // ищем позицию группы в армии, в которой выжили воины

            $unit = $army->getUnitByIndex($startUnitIndex);
            while ($startUnitIndex >=0 && ($finishHealth>$unit->getUnitHealth())) {
                $startUnitIndex--;
                $finishHealth -= $unit->getUnitHealth();
                $unit = $army->getUnitByIndex($startUnitIndex);
            }
            
            // группы, которые шли в бой до найденной с выжившими, считаем погибшими

            $army_units = [];
            $startHealth = 0;
            $i = 0;
            while ($i<$startUnitIndex) {
                $unt = $army->getUnitByIndex($i);
                $army_units[] = $unt->getUnitType() . "(0)";
                $startHealth += $unt->getUnitHealth();
                $i++;
            }

            // считаем количество выживших в первой группе выживших

            $unit = $army->getUnitByIndex($startUnitIndex);
            $count = $unit->calcHealthToCount($finishHealth);
            $army_units[] = $unit->getUnitType() . "(" . $count . ")";

            // считаем, что все группы после первой выжившей - выжили полностью

            $i = $startUnitIndex+1;
            while ($i<$army->getUnitsCount()) {
                $unt = $army->getUnitByIndex($i);
                $army_units[] = (string)$unt;
                $i++;
            }

            $this->armyBattleResult[$key]['armyUnits'] = implode(', ', $army_units);
        }
        $this->armyBattleResult[0]['health'] = $armyFinishHealth[0];
        $this->armyBattleResult[1]['health'] = $armyFinishHealth[1];
        $this->armyBattleResult[0]['Result'] = $this->battleResult($this->armyBattleResult[0]['health'], $this->armyBattleResult[1]['health']);
        $this->armyBattleResult[1]['Result'] = $this->battleResult($this->armyBattleResult[1]['health'], $this->armyBattleResult[0]['health']);
    }
}
