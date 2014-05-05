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
  public function resource_flow()
  {
    return $this->hasOne('WorkFlowResourceflow');
  }

  public function bindingFlow($flow_id){
    $resFlow = new WorkFlowResourceflow();
    $resFlow->flow = Flow::find($flow_id);
    $this->resource_flow()->save($resFlow);
  }

  public function startFlow($auditUsers, $title, $content){
    $this->resource_flow()->goFirst();
    return $this->audit('agreed','',$auditUsers, 0, $title, $content);
  }

  public function status(){
    return $this->resource_flow->status;
  }

  /**
   * Get all audit users has role in current node of this flow
   * @return array $user
   */
  public function getAuditUsers(){
    return $this->resource_flow()->getAuditUsers();
  }

  public function agree($comment, $auditUsers = array(), $title = null, $content = null){
    if($this->status() != 'proceed') return false;

    if($this->resource_flow()->comment('agreed',$comment, $title, $content)->save()) {
      //go next node
      $this->resource_flow()->goNext($auditUsers);
      return true;
    }

    return false;
  }

  public function disagree($callback, $comment, $title = null, $content = null){
    if($this->status() != 'proceed') return false;

    if($this->resource_flow()->comment('agreed',$comment, $title, $content)->save()) {
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
      \DB::table(Config::get('workflow::resource_flow_table'))->where('resource_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }

}
