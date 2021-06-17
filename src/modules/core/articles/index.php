<?php

use GWM\Core\Schema;

$isEnabled = true;

if (!$isEnabled) {
    return false;
} else {
    $stmt = Schema::$PRIMARY_SCHEMA->prepare("SELECT COUNT(*) FROM  ${_ENV['DB_PREFIX']}_articles");
    $stmt->execute();

    $total = $stmt->fetchColumn(0);
    $page = $offset;
    $offset *= 5;
    $paginations = ceil($total / 5) - 1;

    if ($page > $paginations && $paginations != -1) {
        $response->Redirect('/');
    }

    $stmt = $schema->prepare("SELECT * 
    FROM ${_ENV['DB_PREFIX']}_articles
    ORDER BY pinned DESC, created_at DESC
    LIMIT 5  OFFSET $offset");
    $stmt->execute();

    $articles = $stmt->fetchAll(\PDO::FETCH_CLASS, \GWM\Core\Models\Article::class);

    foreach ($articles as &$article) {
        $article->content = html_entity_decode($article->content, ENT_NOQUOTES | ENT_HTML5, 'ISO-8859-1');
        $article->content = preg_replace('~[\r\n]+~', '', $article->content);
        $article->content = stripslashes($article->content);
        $article->{'slug'} = wordwrap(strtolower($article->title), 1, '-', 0);
    }

    return $articles;
}
