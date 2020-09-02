<?php

namespace App\Ciliatus\Common\Enum;

/**
 * @method static PermissionEnum PERMISSION_READ
 * @method static PermissionEnum PERMISSION_WRITE
 * @method static PermissionEnum PERMISSION_ADMIN
 */
class PermissionEnum extends Enum
{

    private const PERMISSION_READ = 'read';
    private const PERMISSION_WRITE = 'write';
    private const PERMISSION_ADMIN = 'admin';

}
