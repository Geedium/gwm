<?php

namespace GWM\Core\Models;

use DateTime;
use GWM\Core\Schema;
use GWM\Core\Models\User;
use GWM\Core\Models\Article;

/**
 * Class Comment
 * 
 * No description.
 * 
 * @package GWM\Commerce\Models
 * @property-read User $user
 * @property-read Article $article
 * @version 1.0.0
 */
class Comment
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
    public string $message;

    /**
     *
     *
     * @var int|null
     */
    protected ?int $user = null;

    /**
     *
     *
     * @var int|null
     */
    protected ?int $article = null;

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
        Schema::$PRIMARY_SCHEMA->Create(Comment::class, 'comments');
    }

    public function __get($propertyName)
    {
        if ($propertyName == 'user') {
            $user = new User();

            if ($this->user ?? false) {
                $user = Schema::$PRIMARY_SCHEMA->Get(User::class, $user, [
                    'user' => $this->user
                ]);
            }

            return $user;
        }

        if ($propertyName == 'article') {
            $article = new Article();

            if ($this->article ?? false) {
                $article = Schema::$PRIMARY_SCHEMA->Get(Article::class, $article, [
                    'article' => $this->article
                ]);
            }

            return $article;
        }

        return null;
    }
    
    

}
