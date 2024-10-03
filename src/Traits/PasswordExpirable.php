<?php

namespace Zenepay\PasswordExpiry\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Imanghafoori\PasswordHistory\Database\PasswordHistory;

trait PasswordExpirable
{
    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class)->latest()->limit(config('password-expiry.previous_passwords_limit'));
    }

    public function isPasswordExpired(): bool
    {
        $days = config('password_history.expiry_days');

        if ($this->passwordHistories()->count()) {
            return Carbon::now()->subDays($days)->gt($this->passwordHistories->first()->created_at);
        }
        return false;
    }

    public function isRepeatedPassword(string $password): bool
    {
        foreach ($this->passwordHistories as $hash) {
            if (Hash::check($password, $hash->password)) {
                return true;
            }
        };
        return false;
    }
}
