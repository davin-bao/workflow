<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: 下午6:08
 */

namespace DavinBao\Workflow;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RoutesCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'workflow:routes';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Append the default Workflow controller routes to the routes.php';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $app = app();
    $app['view']->addNamespace('workflow',substr(__DIR__,0,-17).'views');
  }


  /**
   * Execute the console command.
   *
   * @return void
   */
  public function fire()
  {
    $name = $this->option('rout_name');

    $this->line('');
    $this->info( "Routes file: app/http/routes.php" );

    $message = "A single route to handle every action in a RESTful controller".
        " will be appended to your routes.php file. This may be used with a workflow".
        " controller generated using [-r|--restful] option.";


    $this->comment( $message );
    $this->line('');

    if ( $this->confirm("Proceed with the append? [Yes|no]") )
    {
      $this->line('');

      $this->info( "Appending routes..." );
      if( $this->appendRoutes( $name ) )
      {
        $this->info( "app/http/routes.php Patched successfully!" );
      }
      else{
        $this->error(
          "Coudn't append content to app/routes.php\nCheck the".
          " write permissions within the file."
        );
      }

      $this->line('');

    }
  }


  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return array(
      array('rout_name', null, InputOption::VALUE_OPTIONAL, 'Name of the routes.', 'flow'),
    );
  }


  /**
   * Create the controller
   *
   * @param  string $name
   * @return bool
   */
  protected function appendRoutes( $name = '' )
  {
    $app = app();
    $routes_file = $this->laravel->path.'/http/routes.php';
    $workflow_routes = $app['view']->make('workflow::generators.routes')
      ->with('name', $name)
      ->render();

    if( file_exists( $routes_file ) )
    {
      $fs = fopen($routes_file, 'a');
      if ( $fs )
      {
        fwrite($fs, $workflow_routes);
        $this->line($workflow_routes);
        fclose($fs);
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }


}
