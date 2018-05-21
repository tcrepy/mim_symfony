<?php

namespace App\Service;

Class Tools
{
    public static function UniqueRandomElemFromTab($tab, $quantity) {
        shuffle($tab);
        return array_slice($tab, 0, $quantity);
    }
}