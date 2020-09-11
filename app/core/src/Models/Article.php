<?php

namespace GWM\Core\Models;

/**
 * Undocumented class
 */
class Article
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

    /**
     * Save Object
     *
     * @param [type] $schema
     * @return void
     */
    public function Save(&$schema)
    {
        $schema->Save($this->title, 1);
    }
}