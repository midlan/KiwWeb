<?php

declare(strict_types=1);

namespace KivWeb;

class Role {
    
    const NONE = 0b000;
    const USER = 0b001;
    const REVIEWER = 0b011;
    const ADMIN = 0b101;
}
