<?php

namespace Laraform\Contracts\Validation;

interface Strategy
{
    /**
     * Validate element
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    public function validate(Validator $validator, $prefix = null);

    /**
     * Get custom message for rule
     *
     * @return string
     */
    public function getMessage($rule);

    /**
     * Name of element
     *
     * @return string
     */
    public function name();

    /**
     * Rules of element
     *
     * @return string
     */
    public function rules();

    /**
     * Children of element
     *
     * @return Laraform\Contracts\Elements\Element[]
     */
    public function children();
}