<?php

namespace App\Enums;

enum BookStatus: string
{
    case Read = 'read';
    case Reading = 'reading';
    case TBR = 'tbr';
    case DNF = 'dnf';
}
