Workflow (Laravel4 Package)
========

Workflow package provides a simple way to add audit flow to **Laravel4**.

## Quick start

### Required setup

In the `require` key of `composer.json` file add the following

    "davin-bao/workflow": "dev-master"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'DavinBao\Workflow\WorkflowServiceProvider'` to the end of the `$providers` array

```php
'providers' => array(

    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    ...
    'DavinBao\Workflow\WorkflowServiceProvider',

),
```

At the end of `config/app.php` add `'Workflow'       => 'DavinBao\Workflow\WorkflowFacade'` to the `$aliases` array

```php
'aliases' => array(

    'App'        => 'Illuminate\Support\Facades\App',
    'Artisan'    => 'Illuminate\Support\Facades\Artisan',
    ...
    'Workflow'       => 'DavinBao\Workflow\WorkflowFacade',

),

### Configuration

### 1. Create Table

Now generate the Workflow migration

    $ php artisan workflow:migration

It will generate the `<timestamp>_workflow_setup_tables.php` migration. You may now run it with the artisan migrate command:

    $ php artisan migrate

After the migration, workflow tables will be present.

### 2. Create Controllers

    $ php artisan workflow:controllers

### 3. Create Routes

    $ php artisan workflow:routes

### 4. Link the Model
    class Entry extends Eloquent {
      use \DavinBao\Workflow\HasFlowForResource;
    }

### 5. Add two function for audit log,Audit Flow will record this resource's title and content

		public function getLogTitle()
		{
			return $this->entry_title;
		}
		
		public function getLogContent()
		{
			return $this->entry_content;
		}

### 6. Link the Controller

		class AdminEntryController extends AdminController {
				use \DavinBao\Workflow\HasFlowForResourceController;
		}

### 7. Add roles for this controller

		Route::get('entrys/{entry}/binding', 'AdminEntrysController@getBindingFlow');
		Route::post('entrys/{entry}/binding', 'AdminEntrysController@postBindingFlow');
		Route::get('entrys/{entry}/audit', 'AdminEntrysController@getAudit');
		Route::post('entrys/{entry}/audit', 'AdminEntrysController@postAudit');

### 8. Modify config

Set the propertly values to the `config/auth.php` and `davin-bao/workflow/src/config/config.php` .

## Functions

### Get is binding audit flow

    if(isset($entry->isBinding)) {///is binding, do something }


### Get resource audit status

    $entry->status()


### Show flow Graph

If you want show this resource audit flow status, just input:

    {{ Workflow::makeFlowGraph($entry->flow(), $entry->orderID()) }}

### Show audit flow all details

    {{ Workflow::makeAuditDetail($entry) }}

### Need I audit, show audit button

    if(isset($entry->isBinding) && $entry->isMeAudit()) { /// show audit button }