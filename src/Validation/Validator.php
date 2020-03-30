<?php

namespace Laraform\Validation;

use Illuminate\Contracts\Validation\Validator as IlluminateValidator;
use Laraform\Contracts\Validation\Validator as ValidatorContract;
use Laraform\Contracts\Elements\Element;

class Validator implements ValidatorContract
{
    /**
     * Validator instance
     *
     * @var IlluminateValidator
     */
    public $validator;

    /**
     * Return new instancd of Validator
     *
     * @param IlluminateValidator $validator
     */
    public function __construct(IlluminateValidator $validator) 
    {
        $this->validator = $validator;
        $this->validator->setPresenceVerifier(app('validation.presence'));
    }

    /**
     * Perform validation
     *
     * @param Element $elements
     * @param array $messages
     * @return void
     */
    public function validate(Element $elements, array $messages)
    {
        $this->setData($elements->getValidationData());

        $this->addMessages($messages);

        $elements->validate($this);
    }

    /**
     * Set data for validator
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->validator->setData($data);
    }

    /**
     * Add rules for validator
     *
     * @param array $rules
     * @return void
     */
    public function addRules(array $rules)
    {
        $this->validator->addRules($rules);
    }

    /**
     * Add custom message for validator
     *
     * @param string $attribute
     * @param string $rule
     * @param string $message
     * @return void
     */
    public function addCustomMessage($attribute, $rule, $message)
    {
        $this->addMessage($attribute . '.' . $rule, $message);
    }

    /**
     * Add messages for validator
     *
     * @param array $messages
     * @return void
     */
    protected function addMessages($messages)
    {
        foreach ($messages as $rule => $message) {
            $this->addMessage($rule, $message);
        }
    }

    /**
     * Add message for validator
     *
     * @param string $key
     * @param string $message
     * @return void
     */
    public function addMessage($key, $message)
    {
        $this->validator->setCustomMessages(array_merge($this->validator->customMessages, [
            $key => $message
        ]));
    }

    /**
     * Set implicit rule for validator
     *
     * @param string $attribute
     * @param mixed $rules
     * @param callable $condition
     * @return void
     */
    public function sometimes($attribute, $rules, callable $condition)
    {
        $this->validator->sometimes($attribute, $rules, $condition);
    }

    /**
     * Determine if the validation fails
     *
     * @return bool
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * Return errors of validation
     *
     * @return void
     */
    public function errors()
    {
        return $this->validator->errors()->all();
    }
}