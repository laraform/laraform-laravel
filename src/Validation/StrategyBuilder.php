<?php

namespace Laraform\Validation;

use Laraform\Contracts\Elements\Element;

class StrategyBuilder
{
    /**
     * Element to make the strategy for
     *
     * @var Element
     */
    public $element;

    /**
     * List of available validators
     *
     * @var array
     */
    private $validators = [
        'default' => \Laraform\Validation\Strategies\DefaultStrategy::class,
        'group' => \Laraform\Validation\Strategies\GroupStrategy::class,
    ];

    /**
     * Return new Strategy instance
     *
     * @return Laraform\Contracts\Validation\Strategy
     */
    public function build()
    {
        $validationStrategy = $this->element->validateAs ?: $this->element->behaveAs;

        if (!array_key_exists($validationStrategy, $this->validators)) {
            $validationStrategy = 'default';
        }

        return app()->makeWith($this->validators[$validationStrategy], [
            'builder' => $this
        ]);
    } 

    /**
     * Get the value of element
     */ 
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set the value of element
     *
     * @return  self
     */ 
    public function setElement(Element $element)
    {
        $this->element = $element;

        return $this;
    }
}