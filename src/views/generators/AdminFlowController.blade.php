{{ '<?php' }}

/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-2
 * Time: PM 1:33
 */

class AdminFlowController extends AdminController {

  protected $flow;
  public function __construct(Flow $flow){
    $this->flow = $flow;
  }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {

        // Grab all the flows
        $flows = Flow::paginate(Config::get('app.pagenate_num'));

        // Show the page
        return View::make(Config::get('app.admin_template').'/flows/index', compact('flows'));
    }
  /**
   * Admin dashboard
   *
   */
  public function getCreate()
  {
    $title = Lang::get('admin/infos/title.create');
    $roles = Role::with('users')->get();
    // Show the page
    return View::make(Config::get('app.admin_template').'/flows/create_edit', compact('title', 'roles'));
  }

  public function postCreate(){
    $this->flow = new Flow();
    $this->flow->flow_name = Input::get('flow_name');
    $this->flow->resource_type = Input::get( 'resource_type' );

    if ($this->flow->save(Flow::$rules) )
    {
      $res = Array('result'=>true,'id'=>$this->flow->id, 'message'=>Lang::get('workflow::workflow.saved'));
    }
    else
    {
      // Get validation errors (see Ardent package)
      $error = $this->flow->errors()->all();
      $res = Array('result'=>false,'id'=>1, 'message'=>$error);
    }

    echo json_encode($res);
  }

  public function getEdit($flow) {
    if ( $flow->id )
    {
      $roles = Role::with('users')->get();
      $title = Lang::get('admin/infos/title.create');

      // Show the page
      return View::make(Config::get('app.admin_template').'/flows/create_edit', compact('title', 'flow', 'roles'));
    }
    else
    {
      return Redirect::to('admin/flows')->with('error', Lang::get('workflow::workflow.does_not_exist'));
    }
  }

  public function postEdit($flow){

    // Validate the inputs
    $validator = Validator::make(Input::all(), Flow::$rules);

    // Check if the form validates with success
    if ($validator->passes())
    {
      $flow->flow_name = Input::get( 'flow_name' );
      $flow->resource_type = Input::get( 'resource_type' );
      $nodeIds = Input::get('node_ids');
      if(!$nodeIds || !is_array($nodeIds)){
          $nodeIds = array();
      }
      $flow->updateNodesOrder($nodeIds);

      // Was the role updated?
      if ($flow->save())
      {
        $res = Array('result'=>true,'id'=>$flow->id, 'message'=>Lang::get('workflow::workflow.saved'));
      }
      else
      {
        // Redirect to the role page
        $res = Array('result'=>false,'id'=>$flow->id, 'message'=>$flow->errors()->all());
      }
    }else{
      $res = Array('result'=>false,'id'=>$flow->id, 'message'=>$validator->errors()->all());
    }

    echo json_encode($res);
  }

  public function postCreateNode($flow){
      $node = new Node();
      $node->node_name =Lang::get('workflow::workflow.node').Lang::get('workflow::workflow.name');

       if($flow->nodes()->save($node)){
           $res = Array('result'=>true,'id'=>$node->id,'node_name'=>$node->node_name,'users'=>'','roles'=>'', 'message'=>Lang::get('workflow::workflow.saved'));
       }else{
           $res = Array('result'=>false, 'message'=>$node->errors()->all());
       }
    echo json_encode($res);
  }
}