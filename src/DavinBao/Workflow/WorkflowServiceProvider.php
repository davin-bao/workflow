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
      $this->registerWorkflow();
      $this->registerCommands();
	}

  /**
   * Register the application bindings.
   *
   * @return void
   */
  private function registerWorkflow()
  {
    $this->app->bind('workflow', function($app)
    {
      return new Workflow($app);
    });
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

      $this->app['command.workflow.routes'] = $this->app->share(function($app)
      {
          return new RoutesCommand($app);
      });

      $this->commands(
        'command.workflow.migration',
        'command.workflow.routes'
      );
    }

}
