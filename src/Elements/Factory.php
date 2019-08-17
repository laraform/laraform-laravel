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

        $class = __NAMESPACE__ . '\\' . str_replace('-', '', ucwords($type, '-')) . 'Element';

        if ($name !== null) {
            $schema['name'] = $name;
        }

        return app()->makeWith($class, compact('schema', 'options'));
    }
}