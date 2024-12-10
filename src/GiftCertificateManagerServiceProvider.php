<?php

namespace Larapps\GiftCertificateManager;

use Illuminate\Support\ServiceProvider;

class GiftCertificateManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ .'/database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('giftcertificatepackage.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/gift-certificate-manager'),
            ], 'views');*/

            // Publishing assets.

            // $this->publishes([
            //     __DIR__.'/../resources/js/Pages' => public_path('vendor/gift-certificate-manager'),
            // ], 'assets');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/gift-certificate-manager'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([Console\InstallCommand::class]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'gift-certificate-manager');

        $this->mergeLoggingChannels();

        // Register the main class to use with the facade
        $this->app->singleton('gift-certificate-manager', function () {
            return new GiftCertificateManager;
        });
    }

    private function mergeLoggingChannels()
    {
        // This is the custom package logging configuration we just created earlier
        $packageLoggingConfig = require __DIR__ . '/../config/logging.php';

        $config = $this->app->make('config');

        // For now we manually merge in only the logging channels. We could also merge other logging config here as well if needed.
        // We do this merging manually since mergeConfigFrom() does not do a deep merge and we want to merge only the channels array
        $config->set('logging.channels', array_merge(
            $packageLoggingConfig['channels'] ?? [],
            $config->get('logging.channels', [])
        ));
    }
}
