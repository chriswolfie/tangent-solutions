<?php

namespace App\Rules;

use App\Repositories\Contracts\Base as BaseContract;
use Illuminate\Contracts\Validation\Rule;

class AutonomousExists implements Rule
{
    private $contract;
    private $column_name;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(BaseContract $contract, $column_name)
    {
        $this->contract = $contract;
        $this->column_name = $column_name;
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
        return $this->contract->valueExists($value, $this->column_name);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute value does not exist.';
    }
}
