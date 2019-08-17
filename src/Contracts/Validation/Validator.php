<?php

namespace Laraform\Contracts\Validation;

use Laraform\Contracts\Elements\Element;

interface Validator
{
    /**
     * Perform validation
     *
     * @param Element $elements
     * @param array $messages
     * @return void
     */
    public function validate(Element $elements, array $messages);

    /**
     * Set data for validator
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data);

    /**
     * Add rules for validator
     *
     * @param array $rules
     * @return void
     */
    public function addRules(array $rules);

    /**
     * Add custom message for validator
     *
     * @param string $attribute
     * @param string $rule
     * @param string $message
     * @return void
     */
    public function addCustomMessage($attribute, $rule, $message);

    /**
     * Add message for validator
     *
     * @param string $key
     * @param string $message
     * @return void
     */
    public function addMessage($key, $message);

    /**
     * Set implicit rule for validator
     *
     * @param string $attribute
     * @param mixed $rules
     * @param callable $condition
     * @return void
     */
    public function sometimes($attribute, $rules, callable $condition);

    /**
     * Determine if the validation fails
     *
     * @return bool
     */
    public function fails();

    /**
     * Return errors of validation
     *
     * @return void
     */
    public function errors();
}