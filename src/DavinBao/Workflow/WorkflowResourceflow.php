<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: 上午10:04
 */
namespace DavinBao\Workflow;

use Illuminate\Database\Eloquent\Model;
use LaravelBook\Ardent\Ardent;
use Config;

class WorkFlowResourceflow extends Ardent
{

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table;

  /**
   * Laravel application
   *
   * @var Illuminate\Foundation\Application
   */
  public static $app;
  /**
   * Ardent validation rules
   *
   * @var array
   */
  public static $rules = array(
    'node_order' => 'required|numeric'
  );

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);

    if ( ! static::$app )
      static::$app = app();

    $this->table = static::$app['config']->get('workflow::resource_flow_table');
  }

  public function bindRelationsData($resourceModel){
    self::$relationsData = array(
      'resource'    => array(self::HAS_ONE, $resourceModel, 'foreignKey' => 'resource_id'),
      'flow'    => array(self::BELONGS_TO, static::$app['config']->get('workflow::flow')),
      'resource_nodes'    => array(self::HAS_MANY, "WorkFlowResourcenode"),
    );
  }



  /**
   * Attach resource_nodes to current role
   * @param $resource_nodes
   */
  public function attachNode( $resource_nodes )
  {
    if( is_object($resource_nodes))
      $resource_nodes = $resource_nodes->getKey();

    if( is_array($resource_nodes))
      $resource_nodes = $resource_nodes['id'];

    $this->resource_nodess()->attach( $resource_nodes );
  }

  /**
   * Detach permission form current resource_nodes
   * @param $resource_nodes
   */
  public function detachNode( $resource_nodes )
  {
    if( is_object($resource_nodes))
      $resource_nodes = $resource_nodes->getKey();

    if( is_array($resource_nodes))
      $resource_nodes = $resource_nodes['id'];

    $this->resource_nodess()->detach( $resource_nodes );
  }

  /**
   * Attach multiple resource_nodess to current node
   *
   * @param $resource_nodess
   * @access public
   * @return void
   */
  public function attachNodes($resource_nodess)
  {
    foreach ($resource_nodess as $resource_nodes)
    {
      $this->attachNode($resource_nodes);
    }
  }

  /**
   * Detach multiple resource_nodess from current node
   *
   * @param $resource_nodess
   * @access public
   * @return void
   */
  public function detachNodes($resource_nodess)
  {
    foreach ($resource_nodess as $resource_nodes)
    {
      $this->detachNode($resource_nodes);
    }
  }

  public function getAuditUsers(){
    $auditUsers = array();
    $unauditedNodes = $this->whereHas('resource_nodes', function($q)
    {
      $q->where('result', '=', 'unaudited');
    })->get();
    // if this node is not finished(need another persion audit), return null array
    if($unauditedNodes->count()>0){
      return $auditUsers;
    }
    //if this node is finished, get users in the next node
    $nextOrder = (int)$this->node_order + 1;
    $nextNode = $this->flow()->whereHas('nodes',function($q) use ($nextOrder){
      $q->where('orders','=', $nextOrder);
    })->get()->first();
    //if this is last node , return null array
    if($nextNode && $nextNode->count() <= 0){
      return $auditUsers;
    }

    $auditUsers = $nextNode->users;
    foreach($nextNode->roles as $role){
      foreach($role->users as $user){
        if(!in_array($user, $auditUsers)){
          array_push($auditUsers, $user);
        }
      }
    }
    return $auditUsers;
  }

  public function setAuditUsers($auditUsers = array()){
    foreach($auditUsers as $user){
      $node = new WorkFlowResourcenode();
      $node->user = $user;
      $node->save();
      $this->attachNode($node);
    }
  }

  public function comment($result, $comment, $title = null, $content = null){
    $userId = Auth::user()->id;
    //get current node, save audit infomation
    $currentNode = $this->whereHas('resource_nodes',function($q) use ($userId){
      $q->where('user_id','=', $userId)
        ->where('result','=','unaudited');
    })->get()->first();
    if(!$currentNode || $currentNode->count()<=0){
      return false;
    }
    $currentNode->result = $result;
    $currentNode->comment = $comment;
    $currentNode->orders = $this->node_order;
    $currentNode->recordLog($title, $content);
    if($currentNode->save()){
      return true;
    }
    return false;
  }

  /**
   * return this flow to first
   */
  public function goFirst(){
    //get first user id, if haven't , point to me
    $user = Auth::user();
    $resNode = $this->resource_nodes->where('orders', '=', 0)->get()->first();
    if(!$resNode && $resNode->count <=0){
      $user = $resNode->user;
    }
    $firstNode = new WorkFlowResourcenode();
    $firstNode->user = $user;
    $firstNode->orders = 0;
    $firstNode->save();
    $this->attachNode($firstNode);

    $this->node_order = 0;
    $this->status = 'proceed';

    $auditUsers = array();
    $auditUsers[] = $user;
    $this->setAuditUsers($auditUsers);

  }

  /**
   * go to next node
   * @param $nextAuditUsers
   */
  public function goNext($nextAuditUsers){
    $currentOrder = $this->node_order;
    $nextNodeOrder = $currentOrder + 1;

    $status = 'proceed';
    $maxOrder = $this->flow()->nodes()->count();
    if($nextNodeOrder > $maxOrder){
      $status = 'completed';
    }

    $this->node_order = $nextNodeOrder;
    $this->status = $status;
    $this->setAuditUsers($nextAuditUsers);
  }

  /**
   * set this flow discard
   */
  public function discard(){
    $this->status = 'discard';
    $this->save();
  }

  /**
   * set this flow archived
   */
  public function archived(){
    $this->status = 'archived';
    $this->save();
  }



  /**
   * Before delete all constrained foreign relations
   *
   * @param bool $forced
   * @return bool
   */
  public function beforeDelete( $forced = false )
  {
    try {
      \DB::table(static::$app['config']->get('workflow::resource_node_table'))->where('resource_flow_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }


}