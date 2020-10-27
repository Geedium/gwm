<?php

namespace GWM\Core\Utils;

/**
 * Class Table
 *
 * @package GWM\Core\Utils
 * @version 1.0.0
 */
class Table
{
    protected $fields = [];
    protected $props = [];

    public function Hint($model)
    {
        $reflect = new \ReflectionClass($model);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

        foreach ($props as $prop) $this->fields[] = [
            'name' => $prop->getName(),
            'type' => (string)$prop->getType()
        ];

        return $this;
    }

    public function All(array $props)
    {
        $this->props = $props;
    }

    function __toString()
    {
        return $this->Build();
    }

    public function Build()
    {
        $table = '<table class="table">';

        $table .= '<thead><tr>';

        $row = 1;

        foreach ($this->fields as $key) {
            $table .= '<th scope="'.($row ? 'row' : 'col').'">';

            $row &= ~$row;

            $table .= '<label>'.$key['name'].'</label>';

            $table .= '</th>';
        }

        $table .= '</tr></thead>';

        foreach($this->fields as $key) {
            $table .= '<th scope="col">';

            $table .= '<div class="form-group row">';
            $table .= '<div class="col-sm-10">';

            if($key['type'] == 'string') {
                $table .= '<input type="text" class="form-control">';
            }

            $table .= '</div></div>';

            $table .= '</th>';
        }

        $table .= '<tbody>';

        foreach($this->props as $prop) {
            $table .= '<tr>';

            foreach($this->fields as $field) {

                $var = $prop[$field['name']];

                $table .= "<td>$var</td>";
            }

            $table .= '</tr>';
        }

        $table .= '</tbody>';

        $table .= '</table>';

        return $table;
    }
}