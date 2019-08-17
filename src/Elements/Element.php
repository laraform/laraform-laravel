<?php

namespace Laraform\Elements;

use Laraform\Database\StrategyBuilder as DatabaseBuilder;
use Laraform\Validation\StrategyBuilder as ValidatorBuilder;
use Laraform\Elements\Factory;
use Laraform\Contracts\Validation\Validator;
use Laraform\Contracts\Elements\Element as ElementContract;

class Element implements ElementContract
{
    /**
     * Vue component to render
     * 
     * By default it's the element's type
     *
     * @var string
     */
    public $component;

    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'default';

    /**
     * Defines how the element should be validated
     *
     * @var string
     */
    public $validateAs = null;

    /**
     * Element's key on data
     *
     * @var string
     */
    public $name;

    /**
     * Element's key on entity
     *
     * @var string
     */
    public $attribute;

    /**
     * Whether should persist in db
     *
     * @var bool
     */
    public $persist = true;

    /**
     * Element's data
     *
     * @var array
     */
    public $data = [];

    /**
     * Children of element
     *
     * @var Element[]
     */
    public $children = [];

    /**
     * Element optins
     *
     * @var array
     */
    public $options = [];

    /**
     * Validation rules
     *
     * @var string
     */
    public $rules;

    /**
     * Custom validation messages
     *
     * @var string
     */
    public $messages = [];

    /**
     * Factory instance
     *
     * @var Factory
     */
    protected $factory;

    /**
     * Database strategy instance
     *
     * @var Laraform\Contracts\Database\Strategy
     */
    protected $database;

    /**
     * Validator strategy instance
     *
     * @var Laraform\Contracts\Validation\Strategy
     */
    protected $validator;

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
        $this->factory = $factory;

        $this->schema = $schema;

        $this->options = array_merge($this->options, $options);

        $this->initProperties();

        $this->database = $databaseBuilder
            ->setElement($this)
            ->build();

        $this->validator = $validatorBuilder
            ->setElement($this)
            ->build();
    }

    /**
     * Returns the Vue component's name to render
     *
     * @return string
     */
    public function getComponent()
    {
        if (isset($this->schema['component'])) {
            return $this->schema['component'];
        }

        if ($this->component) {
            return $this->component;
        }

        return $this->getType() . '-element';
    }

    /**
     * Returns element type
     *
     * @return string
     */
    public function getType()
    {
        return $this->schema['type'];
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
    }

    /**
     * Get Element's data with it's key
     *
     * @return array
     */
    public function getData()
    {
        return [
            $this->name => $this->data[$this->name]
        ];
    }

    /**
     * Get Element's value
     *
     * @return array
     */
    public function value()
    {
        return $this->data[$this->name];
    }

    /**
     * Updates the field's data in data property
     *
     * @param mixed $value
     * @return void
     */
    public function updateValue($value)
    {
        $this->data[$this->name] = $value;
    }

    /**
     * Convert Element's data to validation format
     *
     * @return mixed
     */
    public function getValidationData()
    {
        // Being commented because dependent fields like
        // _confirmation will not provide a data for the
        // validator which breaks the validation
        // if (!$this->shouldValidate()) {
        //     return [];
        // }

        if (!$this->presentedIn($this->data)) {
          return [];
        }


        return [
            $this->name => $this->data[$this->name]
        ];
    }

    /**
     * Load element data from target
     *
     * @param object $target
     * @return array
     */
    public function load($target)
    {
        if ($target === null || !$this->shouldPersist()) {
            return [];
        }

        return $this->database->load($target);
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
        if (!$this->shouldPersist()) {
            return;
        }
        
        $this->presentedIn($data)
            ? $this->database->fill($target, $data, $emptyOnNull)
            : ($emptyOnNull ? $this->empty($target) : '');
    }

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
    public function empty($target)
    {
        if (!$this->shouldPersist()) {
            return;
        }

        $this->database->empty($target);
    }

    /**
     * Save freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target)
    {
        if ($target === null || !$this->shouldPersist()) {
            return;
        }

        return $this->database->getNewKeys($target);
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
        if (!$this->shouldValidate()) {
            return;
        }

        $this->validator->validate($validator, $prefix);
    }

    /**
     * Determines if the element should be validated
     *
     * @return boolean
     */
    public function shouldValidate()
    {
        return $this->presentedIn($this->data) && $this->hasRules();
    }

    /**
     * Determines if the element should be persisted
     *
     * @return boolean
     */
    public function shouldPersist()
    {
        return $this->persist;
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

        $schema = $this->correctRules($side, $schema);

        $schema['component'] = $this->getComponent();

        unset($schema['persist']);

        return $schema;
    }

    /**
     * Remove unnecessary rules
     *
     * @param string $side
     * @param array $schema
     * @return array
     */
    protected function correctRules($side, $schema)
    {
        if ($this->hasRules($side)) {
            $schema['rules'] = $this->getRules($side);
        } elseif (array_key_exists('rules', $schema)) {
            unset($schema['rules']);
        }

        return $schema;
    }

    /**
     * Return languages
     * 
     * @return array
     */
    public function getLanguages()
    {
        return $this->options['languages'];
    }

    /**
     * Return rules for side
     *
     * @param string $side
     * @return void
     */
    public function getRules($side = 'backend')
    {
        if (!$this->hasRules($side)) {
            return;
        }

        $rules = $this->rules;

        if ($this->rulesHas($side)) {
            $rules = $rules[$side];
        }

        return $rules;
    }

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return boolean
     */
    public function hasRules($side = 'backend')
    {
        if (is_array($this->rules)) {
            if (array_key_exists($side, $this->rules)) {
                return true;
            }

            if (array_key_exists('frontend', $this->rules) || array_key_exists('backend', $this->rules)) {
                return false;
            }
        }

        return $this->rules !== null;
    }

    /**
     * Return custom message for rule
     *
     * @param string|object $rule
     * @return void
     */
    public function getMessage($rule)
    {
        $ruleName = $rule;

        if (!is_string($rule)) {
          if ($rule instanceof \Illuminate\Validation\Rules\Unique) {
            $ruleName = 'unique';
          }
          elseif ($rule instanceof \Illuminate\Validation\Rules\Exists) {
            $ruleName = 'exists';
          }
        }

        if (array_key_exists($ruleName, $this->messages)) {
            return $this->messages[$ruleName];
        }
    }

    /**
     * Determine if the element is presented in data
     *
     * @param array $data
     * @return boolean
     */
    public function presentedIn(array $data)
    {
        return array_key_exists($this->name, $data);
    }

    /**
     * Store related files and returns it's filenames
     *
     * @param mixed $entity
     * @return array
     */
    public function storeFiles($entity)
    {
       return [];
    }

    /**
     * Returns all files on entity
     *
     * @param mixed $entity
     * @return array
     */
    public function originalFiles($entity)
    {
        return [];
    }

    /**
     * Returns all files based on current data
     *
     * @return array
     */
    public function currentFiles()
    {
        return [];
    }

    /**
     * Determine if rules has certain key
     *
     * @param string $key
     * @param mixed $rules
     * @return void
     */
    protected function rulesHas($key, $rules = null)
    {
        if ($rules === null) {
            $rules = $this->rules;
        }

        return is_array($rules) && array_key_exists($key, $rules);
    }

    /**
     * Initalize class properties
     *
     * @return void
     */
    protected function initProperties()
    {
        $this->name = $this->schema['name'] ?? $this->name;
        $this->rules = $this->schema['rules'] ?? $this->rules;
        $this->messages = $this->schema['messages'] ?? $this->messages;
        $this->persist = $this->schema['persist'] ?? $this->persist;
        $this->attribute = $this->schema['attribute'] ?? $this->name;
    }
}