<?php

namespace App\Enum;

enum SalaryTypeEnum: string
{
    case VARIABLE_SALARY_MONTHLY = 'Variable Salary (monthly)';
    case BASE_SALARY_MONTHLY = 'Base Salary (monthly)';
    case FULL_SALARY_MONTHLY = 'Full Salary (monthly)';
}
