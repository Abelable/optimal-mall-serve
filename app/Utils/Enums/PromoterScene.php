<?php

namespace App\Utils\Enums;

class PromoterScene
{
    const LEVEL_USER = 0;
    const LEVEL_PROMOTER = 1;
    const LEVEL_ORGANIZER_C1 = 2;
    const LEVEL_ORGANIZER_C2 = 3;
    const LEVEL_ORGANIZER_C3 = 4;
    const LEVEL_COMMITTEE = 5;

    const LEVEL_TEXT_MAP = [
        self::LEVEL_USER => '普通用户',
        self::LEVEL_PROMOTER => '推广员',
        self::LEVEL_ORGANIZER_C1 => '组织者C1',
        self::LEVEL_ORGANIZER_C2 => '组织者C2',
        self::LEVEL_ORGANIZER_C3 => '组织者C3',
        self::LEVEL_COMMITTEE => '委员会',
    ];

    const SCENE_USER = 0;
    const SCENE_PROMOTER = 100;
    const SCENE_ORGANIZER_C1 = 201;
    const SCENE_ORGANIZER_C2 = 202;
    const SCENE_ORGANIZER_C3 = 203;
    const SCENE_COMMITTEE = 300;

    const SCENE_TEXT_MAP = [
        self::SCENE_USER => '普通用户',
        self::SCENE_PROMOTER => '推广员',
        self::SCENE_ORGANIZER_C1 => '组织者C1',
        self::SCENE_ORGANIZER_C2 => '组织者C2',
        self::SCENE_ORGANIZER_C3 => '组织者C3',
        self::SCENE_COMMITTEE => '委员会',
    ];
}
