<?php

namespace App\Rules;

use App\Repositories\Contracts\Base as BaseContract;
use Illuminate\Contracts\Validation\Rule;

class AutonomousUniqueRule implements Rule
{
    private $contract;
    private $column_name;
    private $ignore_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(BaseContract $contract, $column_name, $ignore_id = 0)
    {
        $this->contract = $contract;
        $this->column_name = $column_name;
        $this->ignore_id = $ignore_id;
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
        return $this->contract->valueIsUnique($value, $this->column_name, $this->ignore_id);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute value needs to be unique.';
    }
}
