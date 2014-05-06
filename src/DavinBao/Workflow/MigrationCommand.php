<?php namespace DavinBao\Workflow;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'workflow:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the Workflow especifications.';

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
        $this->info( "Tables: flows, nodes, node_role, node_user, resourceflow, resourcenode, resourcelog" );
        $message = "An migration that creates 'flows', 'nodes', 'node_role', 'node_user', 'resourceflow', 'resourcenode', 'resourcelog'".
            " tables will be created in app/database/migrations directory ";

        $this->comment( $message );
        $this->line('');

        if ( $this->confirm("Proceed with the migration creation? [Yes|no]") )
        {
            $this->line('');

            $this->info( "Creating migration..." );
            if( $this->createMigration() )
            {
                $this->info( "Migration successfully created!" );
            }
            else{
                $this->error(
                    "Coudn't create migration.\n Check the write permissions".
                    " within the app/database/migrations directory."
                );
            }

            $this->line('');

        }
    }
    /**
     * Create the migration
     *
     * @param  string $name
     * @return bool
     */
    protected function createMigration()
    {
      $migration_file = $this->laravel->path."/database/migrations/".date('Y_m_d_His')."_workflow_setup_tables.php";
      $app = app();
      $resource_type = '';
      if(is_array($app['config']->get('resource_type'))){
        $resource_type = implode(",", app()['config']->get('workflow::resource_type'));
      }

      $output = $app['view']->make('workflow::generators.migration')->with('res_type',$resource_type)->render();

      if( ! file_exists( $migration_file ) )
      {
        $fs = fopen($migration_file, 'x');
        if ( $fs )
        {
          fwrite($fs, $output);
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