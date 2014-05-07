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
  public static $rules = array();

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);

    if ( ! static::$app )
      static::$app = app();

    $this->table = static::$app['config']->get('workflow::resourceflow_table');
  }

//  public function bindRelationsData($resourceModel){
//    self::$relationsData = array(
//      'resource'    => array(self::BELONGS_TO, $resourceModel),
//      'flow'    => array(self::BELONGS_TO, ),
//      '$resourceNode'    => array(self::HAS_MANY, "WorkFlowResourcenode"),
//    );
//  }

  public function flow() {
    return $this->belongsTo(static::$app['config']->get('workflow::flow'));
  }

  public function resourcenodes() {
    return $this->hasMany(static::$app['config']->get('workflow::resourcenode'), 'resourceflow_id');
  }



  /**
   * Attach $resourceNode to current role
   * @param $resourceNode
   */
  public function attachNode( $resourceNode )
  {
    if( is_object($resourceNode))
      $resourceNode = $resourceNode->getKey();

    if( is_array($resourceNode))
      $resourceNode = $resourceNode['id'];

    $this->resourcenodes()->attach( $resourceNode );
  }

  /**
   * Detach permission form current $resourceNode
   * @param $resourceNode
   */
  public function detachNode( $resourceNode )
  {
    if( is_object($resourceNode))
      $resourceNode = $resourceNode->getKey();

    if( is_array($resourceNode))
      $resourceNode = $resourceNode['id'];

    $this->resourcenodes()->detach( $resourceNode );
  }

  /**
   * Attach multiple resource_nodess to current node
   *
   * @param $resourceNodes
   * @access public
   * @return void
   */
  public function attachNodes($resourceNodes)
  {
    foreach ($resourceNodes as $resourceNode)
    {
      $this->attachNode($resourceNode);
    }
  }

  /**
   * Detach multiple resource_nodess from current node
   *
   * @param $resourceNodes
   * @access public
   * @return void
   */
  public function detachNodes($resourceNodes)
  {
    foreach ($resourceNodes as $resourceNode)
    {
      $this->detachNode($resourceNode);
    }
  }

  public function getAuditUsers(){
    $auditUsers = array();
    $unauditedNodes = $this->whereHas('resourcenodes', function($q)
    {
      $q->where('result', '=', 'unaudited');
    })->get();
    // if this node is not finished(need another persion audit), return null array
    if($unauditedNodes->count()>0){
      return $auditUsers;
    }
    //if this node is finished, get users in the next node
    $nextNode = $this->getNextNode();

    if(!$nextNode || $nextNode->count() <= 0){
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

  public function getNextNode(){
    $nextOrder = (int)$this->node_order + 1;

    $nextNode = \DB::table(static::$app['config']->get('workflow::nodes_table').' AS nodes')
      ->join(static::$app['config']->get('workflow::flows_table').' AS flows', 'flows.id', '=', 'nodes.flow_id')
      ->join(static::$app['config']->get('workflow::resourceflow_table').' AS resourceflows', 'flows.id', '=', 'resourceflows.flow_id')
      ->where('nodes.orders', '=', $nextOrder)
      ->first();
    if(!$nextNode){
        return null;
    }
    $node_relition = static::$app['config']->get('workflow::node');
    $node_instance = new $node_relition;
    return $node_instance::find($nextNode->id);
  }

   public  function getCurrentNode(){
       return $this->flow()->first()->nodes()->where('orders','=', $this->node_orders)->get();
//           $this->whereHas('resourcenodes',function($q) use ($userId){
//           $q->where('user_id','=', $userId)
//               ->where('result','=','unaudited');
//       })->toSql();
   }

    public function getCurrentResourceNode(){
        $userId = static::$app['auth']->user()->id;
        return $this->resourcenodes()->where('user_id','=', $userId)
               ->where('result','=','unaudited')->first();
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
    //get current node, save audit infomation
    $currentNode = $this->getCurrentResourceNode();

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
    $resNode = $this->resourcenodes->where('orders', '=', 0)->get()->first();
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
      \DB::table(static::$app['config']->get('workflow::resourcenode_table'))->where('resourceflow_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }


}