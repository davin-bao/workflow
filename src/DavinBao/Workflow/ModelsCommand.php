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

class ModelsCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'workflow:models';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Append the default Workflow models to /Model/Flow';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $app = app();
    $app['view']->addNamespace('workflow',substr(__DIR__,0,-8).'views');
  }


  /**
   * Execute the console command.
   *
   * @return void
   */
  public function fire()
  {
    $this->line('');
    $this->info( "Models file: model/flow/flow.php, model/flow/node.php, model/flow/resourceFlow.php, model/flow/resourceNode.php, model/flow/resourceLog.php" );

    $message = "A single model, This a base be used with a workflow.";


    $this->comment( $message );
    $this->line('');

    if ( $this->confirm("Proceed with the append? [Yes|no]") )
    {
      $this->line('');

      $this->info( "Appending models..." );

      $models = array(
        'Flow','Node','Resourceflow','Resourcenode','Resourcelog'
      );
      $result = true;
      foreach($models as $model){
        $result = $result && $this->appendModels($model);
      }
      if( $result )
      {
        $this->info( "app/model/flow Patched successfully!" );
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
  protected function appendModels( $name = '' )
  {
    $app = app();
    $model_dir = $this->laravel->path.'/models/Flow';
    $models_file = $this->laravel->path."/models/Flow/$name.php";
    $this->info( $models_file );
    $workflow_models = $app['view']->make('workflow::generators.'.$name)
      ->render();

      if (!file_exists($model_dir)) {
        mkdir($model_dir, 0777, true);
      }

    if( !file_exists( $models_file ) )
    {
      $fs = fopen($models_file, 'x');
      if ( $fs )
      {
        fwrite($fs, $workflow_models);
        $this->line($workflow_models);
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