<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-5-3
 * Time: AM 11:08
 */

 namespace DavinBao\Workflow;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Config;

trait HasNodeForUser
{
    /**
     * Many-to-Many relations with Node
     */
    public function nodes()
    {
        return $this->belongsToMany(Config::get('workflow::node'), Config::get('workflow::node_user_table'));
    }

    public function resource_nodes()
    {
      return $this->hasMany("WorkFlowResourcenode");
    }

  public function myUnAuditedResources(){
    $resource_nodes = $this::whereHas('resource_nodes', function($q)
    {
      $q->where('result', '=', 'unaudited');
    })->get();

    $resources = array();

    foreach ($resource_nodes as $resource_node) {
      $resources[] = $resource_node->flow()->resource();
    }
    return $resources;
  }

  public function myAgreedResources(){
    $resource_nodes = $this::whereHas('resource_nodes', function($q)
    {
      $q->where('result', '=', 'agreed');
    })->get();

    $resources = array();

    foreach ($resource_nodes as $resource_node) {
      $resources[] = $resource_node->flow()->resource();
    }
    return $resources;
  }

  public function myDisagreedResources(){
    $resource_nodes = $this::whereHas('resource_nodes', function($q)
    {
      $q->where('result', '=', 'disagreed');
    })->get();

    $resources = array();

    foreach ($resource_nodes as $resource_node) {
      $resources[] = $resource_node->flow()->resource();
    }
    return $resources;
  }




}