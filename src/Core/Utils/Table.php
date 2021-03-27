<?php

namespace GWM\Core\Utils;

use GWM\Core\Response;
use GWM\Core\Schema;

/**
 * Class Table
 *
 * @package GWM\Core\Utils
 * @version 1.0.0
 */
class Table
{
    protected array $fields = [];
    protected array $props = [];
    private string $model = '';
    private array $data = [];

    private string $action = '';

    public function SET_CRUD($action)
    {
        $this->action = $action;
    }

    public function Handle(Response $response, Schema $schema, $origin = '/', $table = '')
    {
        if (isset($_POST[$this->model . '_submit']) == true) {

            if($this->action == 'CREATE' && $table != '')
            {
                $table = "{$_ENV['DB_PREFIX']}_{$table}";
                $iF = implode(',', $this->fields);
                $iC = count($this->fields);
                
                $v = [];
                for($i = 0; $i < $iC; $i++)
                    $v[] = '?';
                
                $iV = implode(',', $v);

                $stmt = $schema->prepare("INSERT INTO $table ({$iF}) VALUES ({$iV})");
                $stmt->execute();
                
            }

            $_SESSION['POST'] = $_POST;
            $response->Redirect($origin, 301);
        }

        if (isset($_SESSION['POST']) == true) {
            $this->data = $_SESSION['POST'];

            if (isset($_POST[$this->model . '_clear']) == true) {
                $response = new Response();

                unset($_SESSION['POST']);

                $response->setHeaders([
                    'Location: '.$origin
                ]);

                $response->send(302);
                exit(0);
            } else {
                $this->Filter($schema);
            }
        }
    }

    public function Hint($model)
    {
        try {
            $reflect = new \ReflectionClass($model);
        } catch (\ReflectionException $e) {
            echo $e->getMessage();
        }

        if (isset($reflect)) {
            $this->model = 'form_'.strtolower($reflect->getShortName());
            $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        }

        if (isset($props)) {
            foreach ($props as $prop) $this->fields[] = [
                'name' => $prop->getName(),
                'type' => (string)$prop->getType()
            ];
        }

        return $this;
    }

    public function Filter(Schema $schema)
    {
        $sql = @'SELECT * FROM ' . $_ENV['DB_PREFIX'] . '_articles';

        $where = [];

        if (!empty($this->data['form_article_content']) == true) {
            $where[] = '`content` LIKE "%' . $this->data['form_article_content'] . '%"';
        }

        if ((int)($this->data['form_article_id']) > 0) {
            $where[] = '`id` = ' . (int)$this->data['form_article_id'];
        }

        if(!empty($this->data['form_article_title']) == true) {
            $where[] = '`title` LIKE "%' . $this->data['form_article_title'] . '%"';
        }

        for ($i = 0; $i < $n = sizeof($where); $i++) {

            if ($i > 0 && $i < $n) {
                $sql .= ' AND ' . $where[$i];
            } else if ($i == 0) {
                $sql .= ' WHERE ' . $where[$i];
            }
        }

        $pdo = $schema->prepare($sql);

        $pdo->execute();

        $data = $pdo->fetchAll();

        if ($data != false) {
            $this->props = $data;
        } else {
            // Override no matter what.
            $this->fields = [];
        }
    }

    public function All(array $props)
    {
        $this->props = $props;
    }

    function __toString()
    {
        return $this->Build();
    }

    public function Create()
    {
        $builder = '<form method="POST" id="'.$this->model.'" name="'.$this->model.'">';

        foreach ($this->fields as $field) {
            if ($field['type'] == 'string') {
                $cap = ucfirst($field['name']);
                $builder .= '<div>';
                $builder .= "<label for=''>{$cap}</label>";
                $builder .= "<input id='{$field}' type='text'/>";
                $builder .= '</div>';
            }
        }

        $builder .= '<input class="btn btn-dark d-inline" type="submit" form="'.$this->model.'" id="'.$this->model.'_submit'.'" name="'.$this->model.'_submit'.'" value="Create"/>';
        $builder .= '</form>';

        return $builder;
    }

    public function Build()
    {
        if($this->action == 'CREATE')
        {
            return $this->Create();
        }

        $table = '<form method="POST" id="'.$this->model.'" name="'.$this->model.'">';
        $table .= '<table class="wcms-table">';

        if(!isset($this->fields) || empty($this->fields)) {
            $table .= '<div class="alert alert-warning">';

            $table .= 'No entries found.';

            $table .= '</div>';
        } else {
            $table .= '<thead><tr>';

            $row = 1;

            foreach ($this->fields as $key) {
                $table .= '<th scope="'.($row ? 'row' : 'col').'">';

                $row &= ~$row;

                $name = str_replace('_', ' ', $key['name']);

                $table .= '<label>'.$name.'</label>';

                $table .= '</th>';
            }

            $table .= '<th>';
            $table .= '<a href="'. $_SERVER[REQUEST_URI]. '/add" class="btn"><i class="fa fa-plus"></i></button>';
            $table .= '</th>';

            $table .= '<th></th></tr></thead>';

            foreach($this->fields as $key) {
                $table .= '<th scope="col">';

                $table .= '<div class="form-group row">';
                $table .= '<div class="col-sm-10">';

                if($key['name'] == 'created_at') {
                    $name = $key['name'];
                    $table .= '<input type="date" name="' . $this->model . '_' . $name . '" class="form-control">';
                    $table .= '</div></div></th>';
                    continue;
                }

                if($key['type'] == 'string') {
                    $name = $key['name'];
                    $table .= '<input type="text" name="'.$this->model.'_'.$name.'" class="form-control">';
                } else if($key['type'] == 'int') {
                    $name = $key['name'];
                    $table .= '<input type="number" name="' . $this->model . '_' . $name . '" value="0" class="form-control">';
                }

                $table .= '</div></div>';

                $table .= '</th>';
            }
        }

        $table .= '<th scope="col">
            <div class="form-inline">               
                <input class="btn btn-dark d-inline" type="submit" form="'.$this->model.'" id="'.$this->model.'_submit'.'" name="'.$this->model.'_submit'.'" value="Filter"/>
            </div>
            <div class="form-inline">
                <input class="btn btn-warning d-inline" type="submit" form="'.$this->model.'" id="'.$this->model.'_clear'.'" name="'.$this->model.'_clear'.'" value="Clear"/>
            </div>
        </th>';

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
        $table .= '</form>';

        return $table;
    }
}