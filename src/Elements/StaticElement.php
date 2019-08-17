<?php

namespace Laraform\Elements;

use Laraform\Contracts\Validation\Validator;

class StaticElement extends Element
{
    /**
     * Load element data from target
     *
     * @param object $target
     * @return array
     */
    public function load($target)
    {
        return [];
    }

    /**
     * Fill element data to target
     *
     * @param object $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, $data, $emptyOnNull = true)
    {
        // do nothing
    }

    /**
     * Empty element's value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        // do nothing
    }

    /**
     * Save freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target)
    {
        // do nothing
    }

    /**
     * Convert Element's data to validation format
     *
     * @return mixed
     */
    public function getValidationData()
    {
        return [];
    }

    /**
     * Validate element
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    public function validate(Validator $validator, $prefix = null)
    {
        // do nothing
    }

    /**
     * Get schema array
     *
     * @param string $side
     * @return array
     */
    public function getSchema($side)
    {
        $schema = $this->schema;

        $schema['component'] = $this->getComponent();

        return $schema;
    }
}