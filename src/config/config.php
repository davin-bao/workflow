<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Workflow Flow Model
	|--------------------------------------------------------------------------
	|
	| This is the Flow model used by Workflow to create correct relations.  Update
	| the flow if it is in a different namespace.
	|
	*/
	'flow' => '\Flow',

	/*
	|--------------------------------------------------------------------------
	| Entrust Flows Table
	|--------------------------------------------------------------------------
	|
	| This is the Flows table used by Workflow to save roles to the database.
	|
	*/
	'flows_table' => 'flows',

	/*
	|--------------------------------------------------------------------------
	| Entrust Permission Model
	|--------------------------------------------------------------------------
	|
	| This is the Permission model used by Entrust to create correct relations.  Update
	| the permission if it is in a different namespace.
	|
	*/
	'permission' => '\Permission',

	/*
	|--------------------------------------------------------------------------
	| Entrust Permissions Table
	|--------------------------------------------------------------------------
	|
	| This is the Permissions table used by Entrust to save permissions to the database.
	|
	*/
	'permissions_table' => 'permissions',

	/*
	|--------------------------------------------------------------------------
	| Entrust permission_role Table
	|--------------------------------------------------------------------------
	|
	| This is the permission_role table used by Entrust to save relationship between permissions and roles to the database.
	|
	*/
	'permission_role_table' => 'permission_role',

	/*
	|--------------------------------------------------------------------------
	| Entrust assigned_roles Table
	|--------------------------------------------------------------------------
	|
	| This is the assigned_roles table used by Entrust to save assigned roles to the database.
	|
	*/
	'assigned_roles_table' => 'assigned_roles',
);
