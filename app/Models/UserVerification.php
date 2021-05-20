<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class UserVerification extends Model
{
    use SoftDeletes;

    protected $table = 'user_verifications';

    /**
     * Check if user verification is already expired
     * @return boolean
     */
    public function isExpired()
    {
        $now = Carbon::now('Asia/Jakarta');
        if ($this->expired_at >= $now) {
            return false;
        };

        return true;
    }
}