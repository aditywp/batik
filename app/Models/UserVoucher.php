<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserVoucher extends Pivot
{
    protected $table = 'user_vouchers';
    protected $guarded = [];
}