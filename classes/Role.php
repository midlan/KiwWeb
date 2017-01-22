<?php

declare(strict_types=1);

namespace KivWeb;

class Role {
    
    const NONE = 0b000;
    const AUTHOR = 0b001;
    const REVIEWER = 0b010;
    const ADMIN = 0b100;
}
