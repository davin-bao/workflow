<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: 上午10:04
 */
namespace DavinBao\Workflow;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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

   public  function getCurrentNode(){
       return $this->flow()->first()->nodes()->where('orders','=', $this->node_orders)->first();
   }

    public function getAnotherUnAuditResourceNode(){
      $userId = static::$app['auth']->user()->id;
      return $this->resourcenodes()->where('user_id','<>', $userId)
        ->where('result','=','unaudited')->first();
        //Or
//        return $this->whereHas('resourcenodes', function($q) use ($user)
//        {
//          $q->where('result', '=', 'unaudited')
//            ->where('user_id', '<>', $user->id);
//        })->get();
    }
  public function getUnAuditResourceNodes(){
    return $this->resourcenodes()->where('result','=','unaudited')->get();
  }

  public function deleteAllUnAuditResourceNodes(){
    $unAuditNodes = $this->getUnAuditResourceNodes();
    if($unAuditNodes){
      foreach($unAuditNodes as $node){
        $node->delete();
      }
    }
    return true;
  }

  public function getMyUnAuditResourceNode(){
    $userId = static::$app['auth']->user()->id;
    return $this->resourcenodes()->where('user_id','=', $userId)
      ->where('result','=','unaudited')->first();
  }

  public function getNextNode(){
    $nextOrder = (int)$this->node_orders + 1;

    $nextNode = \DB::table(static::$app['config']->get('workflow::nodes_table').' AS nodes')
      ->join(static::$app['config']->get('workflow::flows_table').' AS flows', 'flows.id', '=', 'nodes.flow_id')
      ->join(static::$app['config']->get('workflow::resourceflow_table').' AS resourceflows', 'flows.id', '=', 'resourceflows.flow_id')
      ->where('nodes.orders', '=', $nextOrder)
      ->where('resourceflows.id', '=', $this->id)
      ->select('nodes.id')->first();
    if(!$nextNode){
      return null;
    }
    $node_relition = static::$app['config']->get('workflow::node');
    $node_instance = new $node_relition;
    return $node_instance::find($nextNode->id);
  }

  public function getNextAuditUsers(){
    $nextAuditUsers = new Collection();

    $unauditedNode = $this->getAnotherUnAuditResourceNode();
    // if this node is not finished(need another persion audit), return null array
    if($unauditedNode && $unauditedNode->count()>0){
      return $nextAuditUsers;
    }
    //if this node is finished, get users in the next node
    $nextNode = $this->getNextNode();

    if(!$nextNode || $nextNode->count() <= 0){
      return $nextAuditUsers;
    }
    $nextAuditUsers = $nextNode->users()->get();

    foreach($nextNode->roles()->get() as $role){
      foreach($role->users()->get() as $user){
        if(!$nextAuditUsers->contains($user->id)){
            $nextAuditUsers->add($user);
        }
      }
    }
    return $nextAuditUsers;
  }

  public function setNextAuditUsers($nextAuditUsers = array()){
    foreach($nextAuditUsers as $user){
      $node = new WorkFlowResourcenode();
      $node->user_id = $user->id;
      $node->orders = $this->node_orders;
      $this->resourcenodes()->save($node);
    }
    Log::error($node->errors()->all());
  }

  public function comment($result, $comment, $title = null, $content = null){
    //get current node, save audit infomation
    $currentResourceNode = $this->getMyUnAuditResourceNode();
    if(!$currentResourceNode || $currentResourceNode->count()<=0){
      return false;
    }
    $currentResourceNode->result = $result;
    $currentResourceNode->comment = $comment;
    $currentResourceNode->orders = $this->node_orders;
    $currentResourceNode->recordLog($title, $content);
    if($currentResourceNode->save()){
      return true;
    }
    return false;
  }

  /**
   * return this flow to first
   */
  public function goFirst(){
    //delete others unAndit nodes
    $this->deleteAllUnAuditResourceNodes();
    //get first user id, if haven't , point to me
    $userId = static::$app['auth']->user()->id;
    $resNode = $this->resourcenodes()->where('orders', '=', 0)->get()->first();
    if($resNode && $resNode->count() >0){
      $userId = $resNode->user_id;
    }
    $firstNode = new WorkFlowResourcenode();
    $firstNode->user_id = $userId;
    $firstNode->orders = 0;
    $this->resourcenodes()->save($firstNode);

    $this->node_orders = 0;
    $this->status = 'unstart';
    return $this->save();
  }

  /**
   * go to next node
   * @param $nextAuditUsers
   */
  public function goNext(){
    $currentOrder = $this->node_orders;
    $nextNodeOrder = $currentOrder + 1;

    $status = 'proceed';
    $maxOrder = $this->flow()->first()->nodes()->count();
    if($nextNodeOrder > $maxOrder){
      $status = 'completed';
    }
    $this->node_orders = $nextNodeOrder;
    $this->status = $status;
    $this->save();
    Log::error($this->errors()->all());
  }

  /**
   * set this flow discard
   */
  public function discard(){
    //delete others unAndit nodes
    $this->deleteAllUnAuditResourceNodes();
    //var_dump($this->getUnAuditResourceNodes());exit;
    $this->status = 'discard';
    return $this->save();
  }

  /**
   * set this flow archived
   */
  public function archived(){
    $this->status = 'archived';
    return $this->save();
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