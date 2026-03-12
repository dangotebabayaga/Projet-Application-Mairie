<?php

namespace App\Enum;

enum EtatSignalement: string
{
    case ENREGISTRE = 'enregistre';
    case EN_COURS = 'en_cours';
    case RESOLU = 'resolu';

    public function next(): ?self
    {
        return match($this) {
            self::ENREGISTRE => self::EN_COURS,
            self::EN_COURS => self::RESOLU,
            self::RESOLU => null
        };
    }
}