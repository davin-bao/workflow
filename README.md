Workflow (Laravel5 Package)
========
This package is forked from davin-bao/workflow,I just modify some install configuration so that can be installed in Laravel5, and there may still be some errors, please consider clearfully. -- tao2581
========


Workflow package provides a simple way to add audit flow to **Laravel5**.

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
```

### Configuration

### Create Table

Now generate the Workflow migration

    $ php artisan workflow:migration

It will generate the `<timestamp>_workflow_setup_tables.php` migration. You may now run it with the artisan migrate command:

    $ php artisan migrate

After the migration, workflow tables will be present.

### Create Controllers

    $ php artisan workflow:controllers

### Create Routes

    $ php artisan workflow:routes

### Link the Model
```php
    class Entry extends Eloquent {
      use \DavinBao\Workflow\HasFlowForResource;
    }
```

### Add two function for audit log,Audit Flow will record this resource's title and content
```php
		public function getLogTitle()
		{
			return $this->entry_title;
		}
		
		public function getLogContent()
		{
			return $this->entry_content;
		}
```
### Link the Controller
```php
		class AdminEntryController extends AdminController {
				use \DavinBao\Workflow\HasFlowForResourceController;
		}
```
### Add roles for this controller
```php
		Route::get('entrys/{entry}/binding', 'AdminEntrysController@getBindingFlow');
		Route::post('entrys/{entry}/binding', 'AdminEntrysController@postBindingFlow');
		Route::get('entrys/{entry}/audit', 'AdminEntrysController@getAudit');
		Route::post('entrys/{entry}/audit', 'AdminEntrysController@postAudit');
```
### Modify config

Set the propertly values to the `config/auth.php` and `davin-bao/workflow/src/config/config.php` .

## Functions

### Get is binding audit flow
```php
    if(isset($entry->isBinding)) {///is binding, do something }
```

### Get resource audit status
```php
    $entry->status()
```

### Show flow Graph, show this resource audit flow status

    @if(isset($entry->isBinding))
    {{ Workflow::makeFlowGraph($entry->flow(), $entry->orderID()) }}
    @endif

### Show audit flow all details

     @if(isset($entry->isBinding))
    {{ Workflow::makeAuditDetail($entry) }}
    @endif

### Need I audit, show audit button
```php
    if(isset($entry->isBinding) && $entry->isMeAudit()) { /// show audit button }
```
