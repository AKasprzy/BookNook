<?php

namespace App\Enums;

enum BookFormat: string
{
    case Digital = 'digital';
    case Print = 'print';
    case Audio = 'audio';
}
