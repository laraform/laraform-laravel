<?php

namespace Laraform\Elements;

use Laraform\Database\StrategyBuilder as DatabaseBuilder;
use Laraform\Validation\StrategyBuilder as ValidatorBuilder;
use Laraform\Support\Hash;

class KeyElement extends Element
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    // public $behaveAs = 'key';

    /**
     * Tells if key should be kept secret
     *
     * @var boolean
     */
    public $secret = false;

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

        if (array_key_exists('secret', $schema)) {
            $this->secret = $schema['secret'];
        }
    }

    /**
     * Load element data from target
     *
     * @param object $target
     * @return array
     */
    public function load($target)
    {
        $value = $this->database->load($target); 

        if ($this->isSecret()) {
            $value[$this->attribute] = $this->encrypt($value[$this->attribute]);
        }

        return $value;
    }

    /**
     * Fill element data to target
     *
     * @param object $target
     * @param array $data
     * @param bollean $emptyOnNull
     * @return void
     */
    public function fill($target, $data, $emptyOnNull = true)
    {
        if (!$this->presentedIn($data)) {
            return;
        }

        if ($this->isSecret()) {
            $data[$this->name] = $data[$this->name]
                ? $this->decrypt($data[$this->name])
                : $data[$this->name];
        }

        $this->database->fill($target, $data, $emptyOnNull);
    }

    /**
     * Empty element's value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        // do not empty keys
    }

    /**
     * Save freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target)
    {
        if ($target === null) {
            return;
        }

        $key = $this->database->getNewKeys($target);

        if (!$key) {
            return;
        }

        return $this->isSecret() ? $this->encrypt($key) : $key;
    }

    /**
     * Return value of secret
     *
     * @return boolean
     */
    public function isSecret()
    {
        if (env('APP_ENV') === 'local') {
            return false;
        }

        return $this->secret;
    }

    /**
     * Encrypt value
     *
     * @param mixed $value
     * @return string
     */
    protected function encrypt($value)
    {
        return Hash::encode($value);
    }

    /**
     * Decrypt value
     *
     * @param mixed $value
     * @return string
     */
    protected function decrypt($value)
    {
        return Hash::decode($value);
    }
}