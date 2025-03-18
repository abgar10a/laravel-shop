<?php

namespace App\Services;

use App\Models\Article;

class ArticleService {

    public function createArticle(array $articleData)
    {
        $article = Article::create($articleData);
        return $article;
    }

}
