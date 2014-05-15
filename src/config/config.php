<?php

return array(
  /**
   * This is a switch about whether record resource log
   */
  'record_log' => true,

  /*
  |--------------------------------------------------------------------------
  | Role Model
  |--------------------------------------------------------------------------
  |
  | This is the Role model used by Workflow to create correct relations.  Update
  | the role if it is in a different namespace.
  |
  */
  'role' => '\Role',

  /*
  |--------------------------------------------------------------------------
  | Workflow Roles Table
  |--------------------------------------------------------------------------
  |
  | This is the Roles table used by Workflow to save roles to the database.
  |
  */
  'roles_table' => 'roles',


  'resource_type' => array(
    '"Info"', '"Recruit"'
  ),

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
  | Workflow Flows Table
  |--------------------------------------------------------------------------
  |
  | This is the Flows table used by Workflow to save roles to the database.
  |
  */
  'flows_table' => 'flows',

  /*
  |--------------------------------------------------------------------------
  | Workflow Node Model
  |--------------------------------------------------------------------------
  |
  | This is the Node model used by Workflow to create correct relations.  Update
  | the node if it is in a different namespace.
  |
  */
  'node' => '\Node',

  /*
  |--------------------------------------------------------------------------
  | Workflow Nodes Table
  |--------------------------------------------------------------------------
  |
  | This is the Nodes table used by Workflow to save Nodes to the database.
  |
  */
  'nodes_table' => 'nodes',

  /*
  |--------------------------------------------------------------------------
  | Workflow node_role Table
  |--------------------------------------------------------------------------
  |
  | This is the node_role table used by Workflow to save relationship between nodes and roles to the database.
  |
  */
  'node_role_table' => 'node_role',

  /*
  |--------------------------------------------------------------------------
  | Workflow node_user Table
  |--------------------------------------------------------------------------
  |
  | This is the node_user table used by Workflow to save relationship between nodes and users to the database.
  |
  */
  'node_user_table' => 'node_user',

  /*
  |--------------------------------------------------------------------------
  | Workflow resource_flow Table
  |--------------------------------------------------------------------------
  |
  | This is the resource_flow table used by Workflow to save relationship between resources and flows to the database.
  |
  */
  'resourceflow_table' => 'resourceflow',
  'resourceflow' => '\Resourceflow',

  /*
  |--------------------------------------------------------------------------
  | Workflow resourcenode Table
  |--------------------------------------------------------------------------
  |
  | This is the resourcenode table used by Workflow to save relationship between resources and nodes to the database.
  |
  */
  'resourcenode_table' => 'resourcenode',
  'resourcenode' => '\Resourcenode',

  /*
  |--------------------------------------------------------------------------
  | Workflow resourcelog Table
  |--------------------------------------------------------------------------
  |
  | This is the resourcelog table used by Workflow to save resource's log to the database.
  |
  */
  'resourcelog_table' => 'resourcelog',
  'resourcelog' => '\Resourcelog',

);
