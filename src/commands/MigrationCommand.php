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
        $roles_table = lcfirst($this->option('table'));

        $this->line('');
        $this->info( "Tables: $roles_table, assigned_roles, permissions, permission_role" );
        $message = "An migration that creates '$roles_table', 'assigned_roles', 'permissions', 'permission_role'".
            " tables will be created in app/database/migrations directory";

        $this->comment( $message );
        $this->line('');

        if ( $this->confirm("Proceed with the migration creation? [Yes|no]") )
        {
            $this->line('');

            $this->info( "Creating migration..." );
            if( $this->createMigration( $roles_table ) )
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

}