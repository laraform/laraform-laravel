<?php

namespace Laraform\Database;

class DatabaseBuilder
{
    /**
     * Form instance
     *
     * @var Laraform\Laraform
     */
    public $form;

    /**
     * Return new Database instance
     *
     * @return Database
     */
    public function build()
    {
        return app()->makeWith(Database::class, [
            'builder' => $this
        ]);
    }

    /**
     * Get the value of form
     */ 
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set the value of form
     *
     * @return  self
     */ 
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }
}