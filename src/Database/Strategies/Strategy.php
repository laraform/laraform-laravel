<?php

namespace Laraform\Database\Strategies;

use Laraform\Database\StrategyBuilder;
use Laraform\Contracts\Database\Strategy as StrategyContract;
use Laraform\Contracts\Elements\Element;

class Strategy implements StrategyContract
{
    /**
     * Element to implement the strategy for
     *
     * @var Laraform\Contracts\Elements\Element
     */
    protected $element;

    /**
     * Current entity
     *
     * @var object
     */
    protected $entity;
    
    /**
     * Return new Strategy instance
     *
     * @param StrategyBuilder $builder
     */
    public function __construct(StrategyBuilder $builder)
    {
        $this->setElement($builder->getElement());
    }

    /**
     * Load value to target
     *
     * @param object $target
     * @return void
     */
    public function load($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Fill value to target from data
     *
     * @param object $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Return freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Determine if this has entity
     *
     * @return boolean
     */
    protected function hasEntity()
    {
        return $this->entity !== null;
    }

    /**
     * Set value for Element
     *
     * @param Element $element
     * @return void
     */
    protected function setElement(Element $element)
    {
        $this->element = $element;
    }

    /**
     * Return children of element
     *
     * @return Laraform\Contracts\Elements\Element[]
     */
    protected function children()
    {
        return $this->element->children;
    }

    /**
     * Return value of element from entity
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->entity[$this->attribute()];
    }

    /**
     * Return attribute of element
     *
     * @return string
     */
    protected function attribute()
    {
        return $this->element->attribute;
    }

    /**
     * Return name of element
     *
     * @return string
     */
    protected function name()
    {
        return $this->element->name;
    }
}