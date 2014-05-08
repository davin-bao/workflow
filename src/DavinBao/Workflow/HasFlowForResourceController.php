<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-7
 * Time: PM 5:44
 */

namespace DavinBao\Workflow;


trait HasFlowForResourceController
{

  public function getBindingFlow($entry){
    //get entry's object name
    //
    if ( $entry->id )
    {
      $flows = $entry->getFlows($this->entryName);
      //if this resource has binded flow, go to audit flow
      if($entry->isBindingFlow()){
        return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/audit');
      }

      //if no flow, return success
      if(!$flows || $flows->count() <= 0){
        return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/edit')->with('success', \Lang::get('admin/'.$this->entryName.'/messages.create.success'));
      }else if($flows->count() == 1){
        //if flow is only one, binding it
        $entry->bindingFlow($flows->first()->id);
        return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/audit');
      }
      //if have muliti flows, show list for user select
      return \View::make(\Config::get('app.admin_template').'/flows/binding', compact('entry', 'flows'));
    } else {
      return \Redirect::to('admin/'.$this->entryName.'')->with('error', \Lang::get('admin/'.$this->entryName.'/messages.does_not_exist'));
    }
  }
  public function postBindingFlow($entry){
    if( $entry->id ){
      $entry->bindingFlow(\Input::get( 'flow_id' ));
      return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/audit');
    } else {
      return \Redirect::to('admin/'.$this->entryName.'')->with('error', \Lang::get('admin/'.$this->entryName.'/messages.does_not_exist'));
    }
  }

  public function getAudit($entry){
    if( $entry->id ){
      $nextAuditUsers = $entry->getNextAuditUsers();
      $currentNode = $entry->getCurrentNode();
      //if auditUsers is one person and this entry unstart, auto audited it
      if(count($nextAuditUsers)==1 && $entry->status() == 'unstart'){
        $result = $entry->startFlow($nextAuditUsers, $entry->getLogTitle(), $entry->getLogContent());
        if($result){
          return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/edit')->with('success', \Lang::get('workflow::workflow.action').\Lang::get('workflow::workflow.success'));
        }else{
          return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/edit')->with('error', \Lang::get('workflow::workflow.action').\Lang::get('workflow::workflow.failed_unknown'));
        }
      }else {
        return \View::make(\Config::get('app.admin_template').'/flows/audit', compact('entry','nextAuditUsers','currentNode'));
      }
    } else {
      return \Redirect::to('admin/'.$this->entryName.'')->with('error', \Lang::get('admin/'.$this->entryName.'/messages.does_not_exist'));
    }
  }

  public function postAudit($entry){

    $entry_name = lcfirst(get_class($entry));

    if( $entry->id ){
      $comment = \Input::get( 'comment' );
      $nextAuditUserIds = \Input::get( 'audit_users' );
      $nextAuditUsers = new \Illuminate\Database\Eloquent\Collection();
      if($nextAuditUserIds && count($nextAuditUserIds)>0){
        foreach($nextAuditUserIds as $id){
          $nextAuditUsers->add(\User::find($id));
        }
      }
      $submit = \Input::get( 'submit' );
      switch($submit){
        case 'agree':
          $result = $entry->agree($comment, $nextAuditUsers, $entry->getLogTitle(), $entry->getLogContent());
          break;
        case 'discard':
          $result = $entry->disagree($entry_name .'::discard', $comment, $entry->getLogTitle(), $entry->getLogContent());
          break;
        case 'gofirst':
          $result = $entry->disagree($entry_name .'::goFirst', $comment, $entry->getLogTitle(), $entry->getLogContent());
          break;
      }

      if($result){
        return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/edit')->with('success', \Lang::get('workflow::workflow.action').\Lang::get('workflow::workflow.success'));
      }else{
        return \Redirect::to('admin/'.$this->entryName.'/' . $entry->id . '/edit')->with('error', \Lang::get('workflow::workflow.action').\Lang::get('workflow::workflow.failed_unknown'));
      }
    }
    return \Redirect::to('admin/'.$this->entryName.'')->with('error', \Lang::get('admin/'.$this->entryName.'/messages.does_not_exist'));
  }
}