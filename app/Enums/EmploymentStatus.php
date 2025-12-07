<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case EMPLOYED = 'employed';
    case UNEMPLOYED = 'unemployed';
    case STUDENT = 'student';
    case RETIRED = 'retired';
}
