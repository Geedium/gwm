<?php

namespace GWM\Core\Models;

use DateTime;

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
    public int $id;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $title;

    /**
     * Undocumented variable
     *
     * @var string (text)
     */
    public string $content;

    /**
     * Undocumented variable
     *
     * @var string
     */
    public string $created_at;

    /**
     * @magic
     * @param $schema
     */
    function __construct($schema)
    {
        $this->created_at = (new DateTime())->format("Y-m-d");
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

    public function Create($schema)
    {
        return $schema->Insert('articles', $this->title, $this->created_at, $this->content);
    }

    public function Select($schema)
    {
        return $schema->Select('articles');
    }

    /**
     * Save Object
     *
     * @param $schema
     * @param $id
     * @return void
     */
    public function Save($schema, $id)
    {
        $schema->Save($this->title, $this->content, $id);
    }
}