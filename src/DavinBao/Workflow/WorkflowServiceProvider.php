<?php namespace DavinBao\Workflow;

use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('davin-bao/workflow');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
        $this->registerCommands();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

    public function registerCommands()
    {

        $this->app['command.workflow.migration'] = $this->app->share(function($app)
        {
            return new MigrationCommand($app);
        });

        $this->commands(
            'command.workflow.migration'
        );
    }

}
