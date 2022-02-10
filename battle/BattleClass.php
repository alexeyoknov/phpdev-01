<?php

require_once './ArmyClass.php';

class BattleFieldConditions {

    const BF_CONDITION_ICE = 'ice';
    const BF_CONDITION_RAIN = 'rain';

    const AVAILABLE_CONDITIONS = [
        self::BF_CONDITION_ICE,
        self::BF_CONDITION_RAIN
    ];

    private $conditions = [];


    public static function getAvailableConditions(){
        return self::AVAILABLE_CONDITIONS;
    }
}

class Battle {

    const BATTLE_RESULT = [
        0 => 'LOOSER',
        1 => 'WINNER'
    ];

    private $army = [];
    private $battleFieldConditions = [];

    private function showResult(array $resultBattle = []){
        $table_view = file_get_contents("./table.tpl");

        foreach($resultBattle as $key=>$val)
        {
            if ('army' !== $key)
                $table_view = str_replace("%%$key%%", $val, $table_view);
            else
            {
                for($index=0; $index<count($val); $index++)
                    foreach($val[$index] as $k=>$v){
                        $vl = ( "health" === $k && is_array($v))
                            ? implode('/',$v)
                            : $v;

                        $table_view = str_replace("%%$k" . ($index+1) . "%%", $vl, $table_view);
                    }
            }
        }
        echo $table_view;
    }

    public function __construct(ArmyClass $army1, ArmyClass $army2, array $battleFieldConditions = [])
    {
        $this->army[] = $army1;
        $this->army[] = $army2;
        $this->battleFieldConditions = $battleFieldConditions;
    }

    private function battle(&$startHealth1,&$startHealth2, &$damage1, &$damage2){
        $duration = 0;
        if ($damage1 === 0 && $damage2 === 0)
            return 0;

        while ($startHealth1 > 0 && $startHealth2 > 0)
        {
            $startHealth1 -= $damage2;
            $startHealth2 -= $damage1;
            $duration++;
        }
        return $duration;
    }

    private function battleResult(int $health1,int $health2)
    {
        return self::BATTLE_RESULT[$health1>$health2];
    }

    public function runCommonBattle(array $battleFieldConditions = []){

        // инициализация исходных и предварительных результирующих данных

        $battleFieldConditions = (count($battleFieldConditions)>0)
            ? $battleFieldConditions
            : $this->battleFieldConditions;

        $armyBattleResult = [];
        $armyStartHealth = [];

        foreach($this->army as $key=>$army){
            $armyBattleResult[$key]['commander'] = $army->getCommander();
            $armyBattleResult[$key]['damage'] = $army->calcArmyDamage($battleFieldConditions);
            $armyStartHealth[$key] = $army->calcArmyHealth();
            $armyBattleResult[$key]['health'] = $armyStartHealth[$key];
            $armyBattleResult[$key]['army_units_before'] = $army;
        }


        // непосредственно само сражение

        $duration = $this->battle(
                $armyBattleResult[0]['health'],
                $armyBattleResult[1]['health'],
                $armyBattleResult[0]['damage'],
                $armyBattleResult[1]['damage']
            );

        // вычисление количества выживших юнитов
        // считается, начиная с последней группы войск, которая участвовала в битве

        $armyFinishHealth = [
            $armyBattleResult[0]['health'],
            $armyBattleResult[1]['health']
        ];
        
        foreach($this->army as $key => $army){
            $startUnitIndex = $army->getUnitsCount()-1;
            $startHealth = $armyStartHealth[$key];
            $finishHealth = $armyFinishHealth[$key];

            // ищем позицию группы в армии, в которой выжили воины

            $unit = $army->getUnitByIndex($startUnitIndex);
            while ($startUnitIndex >=0 && ($finishHealth>$unit->getUnitHealth()))
            {
                $startUnitIndex--;
                $finishHealth -= $unit->getUnitHealth();
                $unit = $army->getUnitByIndex($startUnitIndex);
            } 
            
            // группы, которые шли в бой до найденной с выжившими, считаем погибшими

            $army_units = [];
            $startHealth = 0;
            $i = 0;
            while($i<$startUnitIndex){
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
            while($i<$army->getUnitsCount()){
                $unt = $army->getUnitByIndex($i);
                $army_units[] = $unt;
                $i++;
            }

            $armyBattleResult[$key]['army_units'] = implode(', ',$army_units);

        } 
        $armyBattleResult[0]['Result'] = $this->battleResult($armyBattleResult[0]['health'],$armyBattleResult[1]['health']);
        $armyBattleResult[1]['Result'] = $this->battleResult($armyBattleResult[1]['health'],$armyBattleResult[0]['health']);
        
        $this->showResult([
            'BattleName' => 'Common Battle',
            'Hits' => $duration,
            'BattleFieldConditions' => implode(', ', $battleFieldConditions),
            'army' => $armyBattleResult
        ]);
    }

    public function runLineBattle(array $battleFieldConditions = []){

        $battleFieldConditions = (count($battleFieldConditions)>0)
            ? $battleFieldConditions
            : $this->battleFieldConditions;
        
        $armyBattleResult = [];

        $maxUnitsCount = 0; // если разное количество юнитов

        foreach($this->army as $key=>$army){
            $armyBattleResult[$key]['commander'] = $army->getCommander();
            $armyBattleResult[$key]['army_units_before'] = $army;

            $units = $army->getUnits();

            $maxUnitsCount = max($maxUnitsCount, count($units));

            foreach($units as $k=>$unit)
            { 
                $armyBattleResult[$key]['damage'][$k] = $army->calcUnitDamage($unit->getUnitType(),$battleFieldConditions);
                $armyBattleResult[$key]['health'][$k] = $unit->getUnitHealth();    
            }
        }

        //цикл по maxUnitsCount - получить уроны, количество ходов и победителя

        $winsCount = [0,0];
        $unitsResult = [];
        
        for((int) $i=0; $i<$maxUnitsCount; $i++){
            // непосредственно само сражение

            $duration[$i] = $this->battle(
                $armyBattleResult[0]['health'][$i],
                $armyBattleResult[1]['health'][$i],
                $armyBattleResult[0]['damage'][$i],
                $armyBattleResult[1]['damage'][$i]
            );

            if ($armyBattleResult[0]['health'][$i]>$armyBattleResult[1]['health'][$i])
                $winsCount[0]++;
            else
                $winsCount[1]++;            

            $unitsResult[0][$i] = $this->battleResult($armyBattleResult[0]['health'][$i],$armyBattleResult[1]['health'][$i]);
            $unitsResult[1][$i] = $this->battleResult($armyBattleResult[1]['health'][$i],$armyBattleResult[0]['health'][$i]);
        }

        foreach($this->army as $k=>$army){
            foreach($army->getUnits() as $v=>$unit)
                $army_units[$k][$v] = $unit->getUnitType() . "(" 
                    . $unit->calcHealthToCount($armyBattleResult[$k]['health'][$v]) . ")";

        }

    
        $armyBattleResult[0]['Result'] = implode('/',$unitsResult[0]) . ":<br>" . $this->battleResult($winsCount[0],$winsCount[1]);
        $armyBattleResult[1]['Result'] = implode('/',$unitsResult[1]) . ":<br>" . $this->battleResult($winsCount[1],$winsCount[0]);
        $armyBattleResult[0]['army_units'] = implode(', ',$army_units[0]);
        $armyBattleResult[1]['army_units'] = implode(', ',$army_units[1]);
        
        $this->showResult([
            'BattleName' => 'Line By Line Battle',
            'Hits' => implode('/',$duration),
            'BattleFieldConditions' => implode(', ', $battleFieldConditions),
            'army' => $armyBattleResult
        ]);
    }
}
