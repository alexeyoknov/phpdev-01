<?php

namespace War\Battle;

class BattleFieldConditions
{
    const BF_CONDITION_ICE = 'ice';
    const BF_CONDITION_RAIN = 'rain';

    const AVAILABLE_CONDITIONS = [
        self::BF_CONDITION_ICE,
        self::BF_CONDITION_RAIN
    ];

    private $conditions = [];


    public static function getAvailableConditions()
    {
        return self::AVAILABLE_CONDITIONS;
    }
}
