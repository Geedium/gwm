<?php

namespace GWM\Core\Models;

use DateTime;
use GWM\Core\Schema;
use GWM\Core\Models\User;

/**
 * Class Article
 *
 * No description.
 *
 * @package GWM\Commerce\Models
 * @property-read User $author
 * @version 1.0.0
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
     *
     *
     * @var int|null
     */
    protected ?int $author = null;

    /**
     * Undocumented variable
     *
     * @var string (255)
     */
    public string $slug;

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
     */
    function __construct()
    {
        $this->created_at = (new DateTime())->format("Y-m-d");
        Schema::$PRIMARY_SCHEMA->Create(Article::class, 'articles');
    }

    public function __get($propertyName)
    {
        if ($propertyName == 'author') {
            $user = new User();

            if ($this->author ?? false) {
                $user = Schema::$PRIMARY_SCHEMA->Get(User::class, $user, [
                    'id' => $this->author
                ]);
            }

            return $user;
        }

        return null;
    }

    public function setAuthor($userID): void
    {
        $this->author = $userID;
    }

    public static function fromArray(array $array) {
        $instance = new self();
        $instance->setTitle($array['title']);
        $instance->setContent($array['content']);
        $instance->setAuthor($array['author']);
        return $instance;
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

    /** @param $schema
     * @param string $orderBy
     * @return mixed
     * @deprecated
     */
    public function Select($schema, string $orderBy = null)
    {
        return $schema->Select('articles', $orderBy);
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