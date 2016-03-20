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

class ControllersCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'workflow:controllers';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Append the default Workflow controllers to /Controllers/admin';

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
    $this->line('');
    $this->info( "Controllers file: admin/AdminFlowController.php, admin/AdminNodeController.php" );

    $message = "A single controllers, This a base be used with a workflow.";


    $this->comment( $message );
    $this->line('');

    if ( $this->confirm("Proceed with the append? [Yes|no]") )
    {
      $this->line('');

      $this->info( "Appending controller..." );

      $controllers = array(
        'AdminFlowController','AdminNodeController'
      );
      $result = true;
      foreach($controllers as $controller){
        $result = $result && $this->appendControllers($controller);
      }
      if( $result )
      {
        $this->info( "app/http/controllers/admin/AdminFlowController.php and app/controllers/admin/AdminNodeController.php Patched successfully!" );
      }
      else{
        $this->error(
          "Coudn't append content, \nCheck the".
          " write permissions within the file."
        );
      }

      $this->line('');

    }
  }


  /**
   * Create the controller
   *
   * @param  string $name
   * @return bool
   */
  protected function appendControllers( $name = '' )
  {
    $app = app();
    $data_dir = $this->laravel->path.'/Http/Controllers/Admin';
    $datas_file = $this->laravel->path."/Http/Controllers/Admin/$name.php";
    $this->info( $datas_file );
    $workflow_datas = $app['view']->make('workflow::generators.'.$name)
      ->render();
    $workflow_datas = str_replace("{{ '<?php' }}", '<?php', $workflow_datas);
      if (!file_exists($data_dir)) {
        mkdir($data_dir, 0777, true);
      }

    if( !file_exists( $datas_file ) )
    {
      $fs = fopen($datas_file, 'x');
      if ( $fs )
      {
        fwrite($fs, $workflow_datas);
        $this->line($workflow_datas);
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
