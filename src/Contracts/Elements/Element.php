<?php

namespace Laraform\Contracts\Elements;

use Laraform\Contracts\Validation\Validator;

interface Element
{
    /**
     * Returns the Vue component's name to render
     *
     * @return string
     */
    public function getComponent();

    /**
     * Returns element type
     *
     * @return string
     */
    public function getType();

    /**
     * Set Element level data from data array
     *
     * @param array $data
     * @return void
     */
    public function setData($data);

    /**
     * Get Element's data with it's key
     *
     * @return array
     */
    public function getData();

    /**
     * Updates the field's data in data property
     *
     * @param mixed $value
     * @return void
     */
    public function updateValue($value);

    /**
     * Convert Element's data to validation format
     *
     * @return mixed
     */
    public function getValidationData();

    /**
     * Load element data from target
     *
     * @param object $target
     * @return array
     */
    public function load($target);

    /**
     * Fill element data to target
     *
     * @param object $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, $data, $emptyOnNull = true);

    // public function update($target, $data)
    // {
    //     $this->database->update($target, $data);
    // }

    /**
     * Empty element's value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target);

    /**
     * Save freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target);

    /**
     * Validate element
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    public function validate(Validator $validator, $prefix = null);

    /**
     * Determines if the element should be validated
     *
     * @return boolean
     */
    public function shouldValidate();

    /**
     * Get schema array
     *
     * @param string $side
     * @return array
     */
    public function getSchema($side);

    /**
     * Return languages
     * 
     * @return array
     */
    public function getLanguages();

    /**
     * Return rules for side
     *
     * @param string $side
     * @return void
     */
    public function getRules($side = 'backend');

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return boolean
     */
    public function hasRules($side = 'backend');

    /**
     * Return custom message for rule
     *
     * @param string $rule
     * @return void
     */
    public function getMessage($rule);

    /**
     * Determine if the element is presented in data
     *
     * @param array $data
     * @return boolean
     */
    public function presentedIn(array $data);
}