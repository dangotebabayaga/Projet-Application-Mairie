<?php

namespace App\Enum;

enum EtatSignalement: string
{
    case ENREGISTRE = 'enregistré';
    case EN_COURS = 'en cours';
    case RESOLU = 'résolu';

    public function next(): ?self
    {
        return match($this) {
            self::ENREGISTRE => self::EN_COURS,
            self::EN_COURS => self::RESOLU,
            self::RESOLU => null
        };
    }
}