<?php

namespace Zenepay\PasswordExpiry\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class NoPreviousPassword implements ValidationRule
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public static function ofUser($user)
    {
        return new static($user);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->user->isRepeatedPassword($value);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->user->isRepeatedPassword($value)) {
            $fail('The password must not be the same as the current password.');
        }
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('auth.password_used');
    }
}
