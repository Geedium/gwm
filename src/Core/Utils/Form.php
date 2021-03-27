<?php

namespace GWM\Core\Utils;

/**
 * Class Form
 *
 * @package GWM\Core\Utils
 * @version 1.0.0
 */
class Form
{
    protected array $fields = [];

    public function Hint($model)
    {
        $reflect = null;

        try {
            $reflect = new \ReflectionClass($model);
        } catch (\ReflectionException $e) {
            Debug::$log[] = $e->getMessage();
        }

        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

        foreach($props as $prop) $this->fields[] = [
                'name' => $prop->getName(),
                'type' => (string)$prop->getType()
        ];
    }

    function __toString()
    {
        return $this->Build();
    }

    public function Build()
    {
        if(sizeof($this->fields) < 0) {
            return null;
        }

        $form = '<form method="get" action="/">';

        foreach ($this->fields as $key => $value) {
            $form .= '<div class="form-group row">';
            $name = $value['name'];

            $name = \str_replace('_', ' ', $name);
            $name = \ucwords($name);

            if ($value['type'] == 'string') {
                $form .= "<label for='$name'>$name</label>";
                $form .= "<div class='col-sm-10'>";
                $form .= "<input type='text' class='form-control form-control-sm' id='$name' name='GWM[$key][$name]'>";
                $form .= "</div>";
            }

            $form .= '</div>';
        }

        $form .= '<input type="submit" class="btn btn-primary">';

        $form .= '</form>';

        return $form;
    }
}