<?php

namespace Laraform\Validation;

use Laraform\Contracts\Validation\Validation as ValidationContract;
use Laraform\Contracts\Elements\Element;
use Laraform\Contracts\Validation\Validator;

class Validation implements ValidationContract
{
    /**
     * Validator instance
     *
     * @var Validator
     */
    public $validator;

    /**
     * Return new Validation instance
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates elements against given data
     *
     * @param Element $elements
     * @param array $messages
     * @return void
     */
    public function validate(Element $elements, array $messages)
    {
        $this->validator->validate($elements, $messages);
    }

    /**
     * Determine if validation fails
     *
     * @return boolean
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * Return the value of errors
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = [];

        // Filter errors to quarantee that only one
        // of each is represented (required because
        // of multivalue validators)
        foreach ($this->validator->errors() as $error) {
            if (!in_array($error, $errors)) {
                $errors[] = $error;
            }
        }

        return $errors;
    }
}