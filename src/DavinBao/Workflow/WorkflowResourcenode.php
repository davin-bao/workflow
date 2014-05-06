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

class WorkFlowResourcenode extends Ardent
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
    'orders' => 'required|numeric'
  );

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
    $this->table = Config::get('workflow::resourcenode_table');
  }

  public function flow(){
    $this->belongsTo(static::$app['config']->get('workflow::resourceflow'));
  }

  public function user(){
    $this->belongsTo(Config::get('auth.model'));
  }

  public function resourceLog(){
    return $this->hasOne(static::$app['config']->get('workflow::resourcelog'));
  }

  public function recordLog($title, $content){
    if(Config::get('workflow::recordlog')){
      $resourceLog = new WorkFlowResourcelog();
      $resourceLog->title = $title;
      $resourceLog->content = $content;
      if(!$resourceLog->save()){
        return false;
      }
      $this->resourceLog = $resourceLog;
      if($this->save()){
        return true;
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
      \DB::table(Config::get('workflow::resourcelogs_table'))->where('resourcenode_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }
}