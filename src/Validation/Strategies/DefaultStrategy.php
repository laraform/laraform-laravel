<?php

namespace Laraform\Validation\Strategies;

use Illuminate\Validation\Rule;
use Laraform\Contracts\Validation\Validator;

class DefaultStrategy extends Strategy
{
    /**
     * Validate element
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    public function validate(Validator $validator, $prefix = null)
    {
        $name = $prefix . $this->name();

        $this->addRules($validator, $this->rules(), $name);
        $this->addMessages($validator, $this->rules(), $name);
    }
}