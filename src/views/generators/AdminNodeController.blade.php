{{ '<?php' }}


/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-2
 * Time: PM 1:33
 */

class AdminNodeController extends AdminController {

  protected $node;
  public function __construct(Flow $node){
    $this->node = $node;
  }

  public function getEdit($node) {
    if ( $node->id )
    {
        $res = Array('result'=>true,'id'=>$node->id,'node_name'=>$node->node_name,'users'=>$node->users->toArray(),'roles'=>$node->roles->toArray(),'userstr'=>$node->UsersString(),'rolestr'=>$node->RolesString(), 'message'=>Lang::get('workflow::workflow.success'));
    }
    else
    {
        $res = Array('result'=>false,'id'=>$node->id, 'message'=>Lang::get('workflow::workflow.does_not_exist'));
    }
    echo json_encode($res);
  }

  public function postEdit($node){

    // Validate the inputs
    $validator = Validator::make(Input::all(), Node::$rules);

    // Check if the form validates with success
    if ($validator->passes())
    {
      $node->node_name = Input::get( 'node_name' );
        $roles = Input::get('roles');
        if(!is_array($roles)){
            $roles = array();
        }
        $users = Input::get('users');
        if(!is_array($users)){
            $users = array();
        }
        $node->roles()->sync($roles);
        $node->users()->sync($users);

      // Was the role updated?
      if ($node->save())
      {
        $res = Array('result'=>true,'id'=>$node->id, 'message'=>Lang::get('workflow::workflow.saved'));
      }
      else
      {
        // Redirect to the role page
        $res = Array('result'=>false,'id'=>$node->id, 'message'=>$node->errors()->all());
      }
    }else{
      $res = Array('result'=>false,'id'=>$node->id, 'message'=>$validator->errors()->all());
    }

    echo json_encode($res);
  }

    public function postDelete($node)
    {

        if($node->delete()) {
            // Redirect to the role management page
            $res = Array('result'=>true,'id'=>$node->id, 'message'=>Lang::get('workflow::workflow.success'));
        }else{
            $res = Array('result'=>true,'id'=>$node->id, 'message'=>$node->errors()->all());
        }

        echo json_encode($res);
    }



  public function getShowlog(){

    $resourcenode = \Resourcenode::find(\Input::get('id'));
    if ( $resourcenode )
    {
      $log = $resourcenode->resourceLog()->first();
      if($log) {
        $username = $resourcenode->user()->first()->username;
        if(isset($resourcenode->user()->first()->last_name) && isset($resourcenode->user()->first()->first_name)) {
          $username = $resourcenode->user()->first()->last_name.' '.$resourcenode->user()->first()->first_name;
        }
        $result = $resourcenode->result;
        return \View::make(\Config::get('app.admin_template').'/Flows/show_log', compact('log','username','result'));
      }
    }
    return Redirect::to('admin/flows')->with('error', Lang::get('workflow::workflow.does_not_exist'));
  }

}