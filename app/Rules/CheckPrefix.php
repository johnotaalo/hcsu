<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPrefix implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
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
        $prefix = \App\Models\Ref\VehiclePlatePrefix::where('prefix', $value)->where('id', '!=', $this->id)->first();
        if($prefix){
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This prefix already exists elsewhere.';
    }
}
