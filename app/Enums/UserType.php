<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserType extends Enum
{
    const Administrator = 0;
    const FocalPoint = 1;
    const Supervisor = 2;
    const AdminAssistant = 3;
    const Client = NULL;
}
