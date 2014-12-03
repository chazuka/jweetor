<?php namespace Chazzuka\Jweetor;

use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('chazzuka/jweetor');

        $this->app->bind('jwt', function ()
        {
            $config = $this->app['config']->get('jweetor::jwt');

            return new JWTAuthenticator($config);

        }, true);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['jwt'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}