<?php

namespace App\View\Components\Article;

use Closure;
use App\Models\Article;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;

class ViewsBadge extends Component
{
    public int|string $articleId;
    public int $views = 0;

    public function __construct(int|string $articleId)
    {
        $this->articleId = $articleId;

        // cache 1 menit biar hemat query; atur sesuai kebutuhan
        $this->views = Cache::remember("article:views:{$this->articleId}", 60, function () {
            return (int) (Article::whereKey($this->articleId)->value('views') ?? 0);
        });
    }

    public function render(): View|Closure|string
    {
        return view('components.article.views-badge');
    }
}
