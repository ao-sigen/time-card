<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * この名前空間は、あなたのコントローラールートのURL生成時に適用されます。
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * アプリケーションのルートを定義します。
     */
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // 管理者用ルート
        if (file_exists(base_path('routes/admin.php'))) {
            Route::prefix('admin')
                ->middleware('web')
                ->group(base_path('routes/admin.php'));
        }
    }
}
