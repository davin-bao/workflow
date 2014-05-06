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

    public function resourcenodes()
    {
      return $this->hasMany(Config::get('workflow::resourcenode'));
    }

  public function myUnAuditedResources(){
    $resourceNodes = $this::whereHas('resourcenodes', function($q)
    {
      $q->where('result', '=', 'unaudited');
    })->get();

    $resources = array();

    foreach ($resourceNodes as $resourceNode) {
      $resources[] = $resourceNode->flow()->resource();
    }
    return $resources;
  }

  public function myAgreedResources(){
    $resourceNodes = $this::whereHas('resourcenodes', function($q)
    {
      $q->where('result', '=', 'agreed');
    })->get();

    $resources = array();

    foreach ($resourceNodes as $resourceNode) {
      $resources[] = $resourceNode->flow()->resource();
    }
    return $resources;
  }

  public function myDisagreedResources(){
    $resourceNodes = $this::whereHas('resourcenodes', function($q)
    {
      $q->where('result', '=', 'disagreed');
    })->get();

    $resources = array();

    foreach ($resourceNodes as $resourceNode) {
      $resources[] = $resourceNode->flow()->resource();
    }
    return $resources;
  }




}