<?php

namespace Laraform\Validation\Strategies;

use Illuminate\Contracts\Validation\Rule;
use Laraform\Validation\StrategyBuilder;
use Laraform\Support\Arr;
use Laraform\Contracts\Validation\Validator;
use Laraform\Contracts\Validation\Strategy as StrategyContract;

class Strategy implements StrategyContract
{
    /**
     * Element which the strategy is for
     *
     * @var Laraform\Contracts\Elements\Element
     */
    public $element;

    /**
     * Rules where wildcards should be kept
     *
     * @var array
     */
    protected $withWildcards = ['distinct'];

    /**
     * Return new Strategy instance
     *
     * @param StrategyBuilder $builder
     */
    public function __construct(StrategyBuilder $builder)
    {
        $this->element = $builder->element;
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
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Add rules to validator
     *
     * @param Validator $validator
     * @param mixed $rules
     * @param string $name
     * @return void
     */
    protected function addRules(Validator $validator, $rules, $name)
    {
        if (!is_array($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $key => $rule) {
            switch ($this->getRuleType($rule, $key)) {
                case 'callable':
                case 'string':
                    $this->addRule($validator, $rule, $name);
                    break;

                case 'language':
                    $this->addRule($validator, $rule, $name . '.' . $key);
                    break;

                case 'implicit':
                    $this->addImplicitRule($validator, key($rule), $name, $rule[key($rule)]);
                    break;
            }
        }
    }

    /**
     * Add rule to validator
     *
     * @param Validator $validator
     * @param mixed $rule
     * @param string $name
     * @return void
     */
    protected function addRule(Validator $validator, $rule, $name)
    {
        if (is_string($rule)) {
            $rule = $this->fillWildcards($this->removeDebounce($rule), $name);
        }

        if (is_array($rule)) {
            $rule = array_map(function($one) use ($name) {
                return $this->fillWildcards($this->removeDebounce($one), $name);
            }, $rule);
        }

        if (in_array($rule, $this->withWildcards)) {
            $name = $this->addWildcards($name);
        }

        $validator->addRules([
            $name => [$rule]
        ]);
    }

    /**
     * Add implicit rule to validator
     *
     * @param Validator $validator
     * @param string $rule
     * @param string $name
     * @param array|callable $condition
     * @return void
     */
    protected function addImplicitRule(Validator $validator, $rule, $name, $condition)
    {
        if (is_array($condition)) {
            $field = $this->fillWildcards($condition[0], $name);
            $operator = count($condition) == 3 ? $condition[1] : '=';
            $value = count($condition) == 3 ? $condition[2] : $condition[1];

            $condition = function ($input) use ($field, $operator, $value) {
                return $this->compare(Arr::get($input, $field), $value, $operator);
            };
        }

        if (in_array($rule, $this->withWildcards)) {
            $name = $this->addWildcards($name);
        }

        $validator->sometimes($name, $rule, $condition);
    }

    /**
     * Add messages to validator
     *
     * @param Validator $validator
     * @param mixed $rules
     * @param string $name
     * @return void
     */
    protected function addMessages(Validator $validator, $rules, $name)
    {
        if (!is_array($rules)) {
            $rules = array_map(function($rule) {
                return explode(':', $rule)[0];
            }, explode('|', $rules));
        }

        foreach ($rules as $key => $rule) {
            switch ($this->getRuleType($rule, $key)) {
                case 'callable':
                    $this->addCallableMessage($validator, $rule, $name);
                    break;

                case 'string':
                    $this->addMessage($validator, $rule, $name);
                    break;

                case 'language':
                    $this->addMessage($validator, $rule, $name . '.' . $key);
                    break;

                case 'implicit':
                    $this->addMessage($validator, key($rule), $name, $rule[key($rule)]);
                    break;
            }
        }
    }

    /**
     * Add message to validator
     *
     * @param Validator $validator
     * @param string $rule
     * @param string $name
     * @return void
     */
    protected function addMessage(Validator $validator, $rule, $name)
    {
        $message = $this->getMessage($rule);

        if ($message) {
            $validator->addCustomMessage($name, $rule, $message);
        }
    }

    /**
     * Add message to validator
     *
     * @param Validator $validator
     * @param Rule $rule
     * @param string $name
     * @return void
     */
    protected function addCallableMessage(Validator $validator, Rule $rule, $name)
    {
        $message = (new $rule)->message();
        $ruleName = lcfirst((new \ReflectionClass($rule))->getShortName());

        if ($message) {
            $validator->addCustomMessage($name, $ruleName, $message);
        }
    }

    /**
     * Fill asterix values with concrete indexes
     *
     * @param string $fillable - string to be filled
     * @param string $fill - string to get indexes from
     * @return string
     */
    protected function fillWildcards($fillable, $fill)
    {
        preg_match('/\.[0-9]/', $fill, $matches);

        if (count($matches) == 0) {
            return $fillable;
        }

        return vsprintf(str_replace('.*', '%s', $fillable), $matches);
    }

    /**
     * Add wildcard instead of the last index
     *
     * @param string $name
     * @return string
     */
    protected function addWildcards($name)
    {
        return preg_replace('/\d+(?!\d+)/', '*', $name);
    }

    /**
     * Compare two values with a given operator
     *
     * @param mixed $first
     * @param mixed $second
     * @param string $operator
     * @return bool
     */
    protected function compare ($first, $second, $operator) {
        switch ($operator) {
            case "=":
                return $first == $second;
            case "!=":
                return $first != $second;
            case ">=":
                return $first >= $second;
            case "<=":
                return $first <= $second;
            case ">":
                return $first > $second;
            case "<":
                return $first < $second;
        }

        throw new \InvalidArgumentException('Unknown operator:' . $operator);
    }

    /**
     * Determine the type of rule
     *
     * @param mixed $rule
     * @param mixed $key
     * @return string
     */
    protected function getRuleType($rule, $key)
    {
        if (is_numeric($key)) {
            if (is_array($rule)) {
                return 'implicit';
            } elseif ($rule instanceof Rule) {
                return 'callable';
            } else {
                return 'string';
            }
        } else {
            return 'language';
        }
    }

    /**
     * Removes debounce param from rule
     *
     * @param [string] $rule
     * @return string
     */
    protected function removeDebounce($rule) {
      if (!is_string($rule)) {
        return $rule;
      }

      return preg_replace('/[:,]?debounce=\d*[^,]/', '', $rule);
    }

    /**
     * Get custom message for rule
     *
     * @return string
     */
    public function getMessage($rule)
    {
        return $this->element->getMessage($rule);
    }

    /**
     * Name of element
     *
     * @return string
     */
    public function name()
    {
        return $this->element->name;
    }

    /**
     * Rules of element
     *
     * @return string
     */
    public function rules()
    {
        return $this->element->getRules();
    }

    /**
     * Children of element
     *
     * @return Laraform\Contracts\Elements\Element[]
     */
    public function children()
    {
        return $this->element->children;
    }
}