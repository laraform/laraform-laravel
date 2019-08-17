<?php

namespace Laraform\Contracts\Validation;

use Laraform\Contracts\Elements\Element;

interface Validation
{
    /**
     * Validate elements against data
     *
     * @param Laraform\Contracts\Element[] $elements
     * @param array $messages
     * @return void
     */
    public function validate(Element $elements, array $messages);

    /**
     * Determine if validation fails
     *
     * @return boolean
     */
    public function fails();

    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors();
}