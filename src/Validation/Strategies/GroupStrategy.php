<?php

namespace Laraform\Validation\Strategies;

use Laraform\Contracts\Validation\Validator;

class GroupStrategy extends Strategy
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
        foreach ($this->children() as $name => $child) {
            $child->validate($validator, $prefix);
        }
    }
}