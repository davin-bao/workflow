<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: 上午9:58
 */
namespace DavinBao\Workflow;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Config;

trait HasFlowForResource
{

  public $isBinding = true;
  private $resourceFlow = false;
  /**
   * Many-to-Many relations with Node
   */
  public function resourceflow()
  {
    $resourceType = lcfirst(get_class($this)).'s';
    if(!$this->resourceFlow){

      $this->resourceFlow =  WorkFlowResourceflow::where('resource_id','=',$this->id)->where('resource_type', '=',$resourceType)->first();
    }
    return $this->resourceFlow;
  }

  public function getFlows($resource_type){
    return WorkFlowFlow::where('resource_type','=',$resource_type)->get();
  }

  public function isBindingFlow() {
    return $this->resourceflow() != false;
  }

  public function bindingFlow($flow_id){
    $resFlows = WorkFlowResourceflow::where('flow_id','=',$flow_id)->where('resource_id','=',$this->id)->get();
    if($resFlows->count()<=0){
      $resFlow = new WorkFlowResourceflow;
      $resFlow->flow_id = $flow_id;
      $resFlow->resource_type = Flow::find($flow_id)->resource_type;
      $resFlow->resource_id = $this->id;
      $resFlow->save();
    }
  }

  public function startFlow($auditUsers, $title, $content){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    $resFlow->goFirst();
    return $this->agree('',$auditUsers, $title, $content);
  }

  public function status(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->status;
  }

  public function flow(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->flow()->first();
  }

  public function orderID(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->node_orders;
  }

  /**
   * Get all audit users has role in current node of this flow
   * @return array $user
   */
  public function getNextAuditUsers(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->getNextAuditUsers();
  }

    public function getNextNode(){
      $resFlow = $this->resourceflow();
      if(!$resFlow) return false;
        return $resFlow->getNextNode();
    }

    public function getCurrentNode(){
      $resFlow = $this->resourceflow();
      if(!$resFlow) return false;
      return $resFlow->getCurrentNode();
    }

    public function isMeAudit(){
      $resFlow = $this->resourceflow();
      if(!$resFlow) return false;
        $myNode = $resFlow->getMyUnAuditResourceNode();
      return $myNode != false;
    }

  public function agree($comment, $auditUsers, $title = null, $content = null){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    if($resFlow->comment('agreed',$comment, $title, $content)) {
      //go next node
      //if have not next resource node ,to go next node
      $unauditedNode = $resFlow->getAnotherUnAuditResourceNode();
      if(!$unauditedNode || $unauditedNode->count()<=0){
        $resFlow->goNext();
      }

      if($auditUsers && $auditUsers->count()>0) {
        $resFlow->setNextAuditUsers($auditUsers);
      }
      return true;
    }

    return false;
  }

  public function disagree($callback, $comment, $title = null, $content = null){
    if($this->status() != 'proceed') return false;
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    if($resFlow->comment('disagreed',$comment, $title, $content)) {
      //run callback
      if ($callback) {
        return call_user_func($callback);
      }
    }

    return false;
  }

  public function shouldPublish(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    if($resFlow->getAnotherUnAuditResourceNode() == false && $resFlow->getNextNode() == null){
      return true;
    }
    return false;
  }

  public function discard(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->discard();
  }

  public function goFirst(){
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    return $resFlow->goFirst();
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
      \DB::table(Config::get('workflow::resourceflow_table'))->where('resource_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }

  public function getAuditByTimeLine(){
    $auditsByDate = array();
    $flow = $this->flow();
    $resFlow = $this->resourceflow();
    if(!$resFlow) return false;
    $nodes = $resFlow->resourcenodes()->get();
    foreach ($nodes as $node) {
      $username = $node->user()->first()->username;
      if(isset($node->user()->first()->last_name) && isset($node->user()->first()->first_name)) {
        $username = $node->user()->first()->last_name.' '.$node->user()->first()->first_name;
      }

      $nodename = \Lang::get('workflow::workflow.push');
      if((int)$node->orders>0){
        $nodename = $flow->nodes()->where('orders','=',$node->orders)->first()->node_name;
      }

      $auditsByDate[$node->updated_at->toDateString()][$node->updated_at->format('H:i')]['id'] = $node->id;
      $auditsByDate[$node->updated_at->toDateString()][$node->updated_at->format('H:i')]['username'] = $username;
      $auditsByDate[$node->updated_at->toDateString()][$node->updated_at->format('H:i')]['nodename'] = $nodename;
      $auditsByDate[$node->updated_at->toDateString()][$node->updated_at->format('H:i')]['result'] = $node->result;
      $auditsByDate[$node->updated_at->toDateString()][$node->updated_at->format('H:i')]['comment'] = $node->comment;
    }
    return $auditsByDate;
  }

}
