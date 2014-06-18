<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-2
 * Time: am 11:11
 */
 namespace DavinBao\Workflow;

use LaravelBook\Ardent\Ardent;
use Config;

class WorkFlowNode extends Ardent
{

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table;

  /**
   * Ardent validation rules
   *
   * @var array
   */
  public static $rules = array(
    'node_name' => 'required|between:1,20'
  );

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
    $this->table = Config::get('workflow::nodes_table');
  }
  /**
   * Many-to-One relations with flows
   */
  public function flow()
  {
    return $this->belongsTo(Config::get('workflow::flow'));
  }

  /**
   * Many-to-Many relations with Users
   */
  public function users()
  {
    return $this->belongsToMany(Config::get('auth.model'), Config::get('workflow::node_user_table'));
  }

    public function UsersString($count=3){
        $userString = "";
        foreach($this->users->take($count) as $user){
          $username = $user->name();
            if(strlen($username)>0) {
                $userString = $userString . $username . ",";
            }
        }
        return strlen($userString)>0?$userString.'...':'';
    }

  /**
   * Many-to-Many relations with Roles
   */
  public function roles()
  {
    return $this->belongsToMany(Config::get('workflow::role'), Config::get('workflow::node_role_table'));
  }

    public function RolesString($count=3){
        $roleString = "";
        foreach($this->roles->take($count) as $role){
            if(strlen($role->name)>0) {
                $roleString = $roleString . $role->name . ",";
            }
        }
        return strlen($roleString)>0?$roleString.'...':'';
    }
    //本人是否有权审批
    public function isContainsMe(){
        foreach($this->users as $user){
            if($user->id = \Auth::user()->id){
                return true;
            }
        }
        foreach($this->roles as $role){
            foreach($role->users as $user){
                if($user->id = \Auth::user()->id){
                    return true;
                }
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
      \DB::table(Config::get('workflow::node_user_table'))->where('node_id', $this->id)->delete();
      \DB::table(Config::get('workflow::node_role_table'))->where('node_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }

  /**
   * Attach user to current role
   * @param $user
   */
  public function attachUser( $user )
  {
    if( is_object($user))
      $user = $user->getKey();

    if( is_array($user))
      $user = $user['id'];

    $this->users()->attach( $user );
  }

  /**
   * Detach permission form current user
   * @param $user
   */
  public function detachUser( $user )
  {
    if( is_object($user))
      $user = $user->getKey();

    if( is_array($user))
      $user = $user['id'];

    $this->users()->detach( $user );
  }

  /**
   * Attach multiple users to current node
   *
   * @param $users
   * @access public
   * @return void
   */
  public function attachUsers($users)
  {
    foreach ($users as $user)
    {
      $this->attachUser($user);
    }
  }

  /**
   * Detach multiple users from current node
   *
   * @param $users
   * @access public
   * @return void
   */
  public function detachUsers($users)
  {
    foreach ($users as $user)
    {
      $this->detachUser($user);
    }
  }

  /**
   * Attach role to current role
   * @param $role
   */
  public function attachRole( $role )
  {
    if( is_object($role))
      $role = $role->getKey();

    if( is_array($role))
      $role = $role['id'];

    $this->roles()->attach( $role );
  }

  /**
   * Detach permission form current role
   * @param $role
   */
  public function detachRole( $role )
  {
    if( is_object($role))
      $role = $role->getKey();

    if( is_array($role))
      $role = $role['id'];

    $this->roles()->detach( $role );
  }

  /**
   * Attach multiple roles to current node
   *
   * @param $roles
   * @access public
   * @return void
   */
  public function attachRoles($roles)
  {
    foreach ($roles as $role)
    {
      $this->attachRole($role);
    }
  }

  /**
   * Detach multiple roles from current node
   *
   * @param $roles
   * @access public
   * @return void
   */
  public function detachRoles($roles)
  {
    foreach ($roles as $role)
    {
      $this->detachRole($role);
    }
  }
}