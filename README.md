workflow
========

this is a workflow package for laravel

### Table create

Now generate the Workflow migration

    $ php artisan workflow:migration

It will generate the `<timestamp>_workflow_setup_tables.php` migration. You may now run it with the artisan migrate command:

    $ php artisan migrate

After the migration, workflow tables will be present.