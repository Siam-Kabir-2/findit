<?php

namespace App\Providers;

use App\Database\OracleConnection;
use App\Database\OracleConnector;
use App\Models\Item;
use App\Policies\ItemPolicy;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('oracle', function ($connection, $database, $prefix, $config) {
            $connector = new OracleConnector;
            $pdo = $connector->connect($config);

            return new OracleConnection($pdo, $database, $prefix, $config);
        });
    }

    public function boot(): void
    {
        Gate::policy(Item::class, ItemPolicy::class);
    }
}
