<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PlateStatus extends Enum
{
    const Pending = 0;
    const Ordered = 1;
    const Assigned = 2;
    const NotAssigned = 3;
}
