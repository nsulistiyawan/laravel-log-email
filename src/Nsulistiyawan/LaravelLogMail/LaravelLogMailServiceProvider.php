<?php namespace Nsulistiyawan\LaravelLogMail;

use Illuminate\Support\ServiceProvider;

class LaravelLogMailServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    public function boot(){
        $this->package('nsulistiyawan/laravel-log-mail', 'logemail');
        $app = $this->app;
        // Listen to log messages.
        $app['log']->listen(function ($level, $message, $context) use ($app) {
            if($app->environment() == 'production') {
                $app['Nsulistiyawan\LaravelLogMail\EmailHandle']->sendEmail($level, $message, $app);
            }
        });

    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $app = $this->app;
        $this->app['Nsulistiyawan\LaravelLogMail\EmailHandle'] = $this->app->share(function ($app) {
            if($app->environment() == 'production') {
                $level = !empty(getenv('LOG_MAIL_LEVEL')) ? getenv('LOG_MAIL_LEVEL') : 'error';
                $recipients = array_map('trim', explode(',', getenv('LOG_MAIL_RECIPIENTS')));
                return new EmailHandler($recipients, $level);
            }
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return array('logemail');
	}

}
