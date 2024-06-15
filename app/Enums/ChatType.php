<?php

namespace App\Enums;

enum ChatType: int
{
    case PRIVATE  = 0;
    case GROUP = 1;
    case PERSONAL = 2;
}
