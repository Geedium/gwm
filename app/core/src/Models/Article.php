<?php

namespace GWM\Core\Models;

/**
 * Undocumented class
 */
class Article extends \GWM\Core\Model implements \GWM\Core\IModel
{
    /**
     * Undocumented variable
     *
     * @var int (primary)
     */
    public $id;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public $title;

    /**
     * Undocumented variable
     *
     * @var string (text)
     */
    public $content;

    /**
     * Undocumented variable
     *
     * @var DateTime
     */
    public $created_at;

    /**
     * @magic
     */
    function __construct(&$schema)
    {
        $this->created_at = (new \DateTime())->format("Y-m-d");
        $schema->Create(Article::class, 'articles');
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @return void
     */
    public function setContent(string $content) : void
    {
        $this->content = $content;
    }

    public function Create(&$schema)
    {
        return $schema->Insert('articles', $this->title, $this->created_at, $this->content);
    }

    public function Select(&$schema)
    {
        return $schema->Select('articles');
    }

    /**
     * Save Object
     *
     * @param [type] $schema
     * @return void
     */
    public function Save(&$schema, $id)
    {
        $schema->Save($this->title, $this->content, $id);
    }
}