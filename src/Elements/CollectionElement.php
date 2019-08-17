<?php

namespace Laraform\Elements;

use Laraform\Database\StrategyBuilder as DatabaseBuilder;
use Laraform\Validation\StrategyBuilder as ValidatorBuilder;
use Laraform\Contracts\Validation\Validator;

class CollectionElement extends Element
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'group';

    /**
     * Return new Element instance
     *
     * @param array $schema
     * @param array $options
     * @param Factory $factory
     * @param DatabaseBuilder $databaseBuilder
     * @param ValidatorBuilder $validatorBuilder
     */
    public function __construct($schema, $options, Factory $factory, DatabaseBuilder $databaseBuilder, ValidatorBuilder $validatorBuilder)
    {
        parent::__construct($schema, $options, $factory, $databaseBuilder, $validatorBuilder);

        $this->setChildren();
    }

    /**
     * Set Element level data from data array
     *
     * @param array $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;

        foreach ($this->children as $child) {
            $child->setData($this->data);
        }
    }

    /**
     * Get Element's data with it's key
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        foreach ($this->children as $child) {
            $data = array_merge($data, $child->getData());
        }

        return $data;
    }

    /**
     * Convert Element's data to validation format
     *
     * @return mixed
     */
    public function getValidationData()
    {
        $data = [];

        foreach ($this->children as $child) {
            $data = array_merge($data, $child->getValidationData());
        }

        return $data;
    }

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return boolean
     */
    public function hasRules($side = 'backend')
    {
        foreach ($this->children as $child) {
            if ($child->hasRules($side)) {
                return true;
            }
        }

        return false;
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
        return $this->validator->validate($validator, $prefix);
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

        foreach ($this->children as $child) {
            $schema[$child->name] = $child->getSchema($side);
        }

        return $schema;
    }

    /**
     * Determine if the element is presented in data
     *
     * @param array $data
     * @return boolean
     */
    public function presentedIn(array $data)
    {
        return true;
    }

    /**
     * Return child by key
     *
     * @param string $key
     * @return Element
     */
    public function getChildByKey($key)
    {
        return $this->children[$key];
    }

    /**
     * Determine if has child with key
     *
     * @param string $key
     * @return boolean
     */
    public function hasChildWithKey($key)
    {
        return array_key_exists($key, $this->children);
    }

    /**
     * Set children based on schema
     *
     * @return void
     */
    protected function setChildren()
    {
        foreach ($this->schema as $name => $schema) {
            $this->addChild($this->makeChild($schema, $name));
        }
    }

    /**
     * Make new Element instance
     *
     * @param array $schema
     * @param string $name
     * @return Element
     */
    protected function makeChild($schema, $name)
    {
        return $this->factory->make($schema, $name, $this->options);
    }

    /**
     * Add child to children
     *
     * @param Element $child
     * @return void
     */
    protected function addChild(Element $child)
    {
        $this->children[$child->name] = $child;
    }

    /**
     * Initalize class properties
     *
     * @return void
     */
    protected function initProperties()
    {

    }
}