<?php

namespace App\Providers;

use App\Models\ArticleNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /* --------------------------  NOTIFIKASI ARTIKEL  -------------------------- */
        View::composer('AdminPage.App.navbar', function ($view) {   // 👈  path yang benar
            $unread = ArticleNotification::where('status', ArticleNotification::UNREAD);

            $view->with([
                'notifCount' => $unread->count(),
                'notifList'  => ArticleNotification::latest()
                                 ->take(5)            // tampilkan 5 terakhir
                                 ->get(),
            ]);
        });

        /* -------------------------------------------------------------------------- */
        Paginator::useBootstrapFour();
    }
}
