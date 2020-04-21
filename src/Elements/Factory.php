<?php

namespace Laraform\Elements;

class Factory
{
    /**
     * Make new Element
     * 
     * @param array $schema
     * @param string $name
     * @param array $options
     * @return Laraform\Contracts\Elements\Element
     */
    public function make($schema, $name = null, $options)
    {
        $type = $schema['type'] ?? 'collection';

        if ($name !== null) {
            $schema['name'] = $name;
        }

        return app()->makeWith($this->getClass($type), compact('schema', 'options'));
    }

    /**
     * Returns element class
     *
     * @param string $type
     * @return string
     */
    protected function getClass($type) {
      $elements = config('laraform.elements');

      if (!empty($elements) && array_key_exists($type, $elements)) {
        return $elements[$type];
      }

      return __NAMESPACE__ . '\\' . str_replace('-', '', ucwords($type, '-')) . 'Element';
    }
}