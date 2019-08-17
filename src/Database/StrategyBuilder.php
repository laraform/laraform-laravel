<?php

namespace Laraform\Database;

use Laraform\Contracts\Elements\Element;

class StrategyBuilder
{
    /**
     * Element instance
     *
     * @var Element
     */
    public $element;

    /**
     * Available strategies
     *
     * @var \Laraform\Contracts\Database\Strategy[]
     */
    private $strategies = [
        'default' => \Laraform\Database\Strategies\DefaultStrategy::class,
        'group' => \Laraform\Database\Strategies\GroupStrategy::class,
    ];

    /**
     * Return new Strategy
     *
     * @return Laraform\Contracts\Database\Strategy
     */
    public function build()
    {
        $strategy = array_key_exists($this->element->behaveAs, $this->strategies)
            ? $this->strategies[$this->element->behaveAs]
            : $this->strategies['default'];

        return app()->makeWith($strategy, [
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