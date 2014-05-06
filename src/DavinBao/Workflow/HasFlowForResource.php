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
  /**
   * Many-to-Many relations with Node
   */
  public function resourceflow()
  {
    return WorkFlowResourceflow::where('resource_id','=',$this->id)->first(); //$this->hasOne('DavinBao\Workflow\WorkFlowResourceflow', 'resource_id');
  }

  public function getFlows($resource_type){
    return WorkFlowFlow::where('resource_type','=',$resource_type)->get();
  }

  public function bindingFlow($flow_id){
    $resFlows = WorkFlowResourceflow::where('flow_id','=',$flow_id)->where('resource_id','=',$this->id)->get();
    if($resFlows->count()<=0){
      $resFlow = new WorkFlowResourceflow;
      $resFlow->flow_id = $flow_id;
      $resFlow->resource_id = $this->id;
      $resFlow->save();
    }
  }

  public function startFlow($auditUsers, $title, $content){
    $this->resourceflow()->goFirst();
    return $this->agree('',$auditUsers, 0, $title, $content);
  }

  public function status(){
    return $this->resourceflow()->status;
  }

  /**
   * Get all audit users has role in current node of this flow
   * @return array $user
   */
  public function getAuditUsers(){
    return $this->resourceflow()->getAuditUsers();
  }

  public function getNextNode(){
    return $this->resourceflow()->getNextNode();
  }

  public function agree($comment, $auditUsers = array(), $title = null, $content = null){
    if($this->status() != 'proceed') return false;

    if($this->resourceflow()->comment('agreed',$comment, $title, $content)->save()) {
      //go next node
      $this->resourceflow()->goNext($auditUsers);
      return true;
    }

    return false;
  }

  public function disagree($callback, $comment, $title = null, $content = null){
    if($this->status() != 'proceed') return false;

    if($this->resourceflow()->comment('agreed',$comment, $title, $content)->save()) {
      //run callback
      if ($callback) {
        return call_user_func($callback);
      }
    }

    return false;
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

}
