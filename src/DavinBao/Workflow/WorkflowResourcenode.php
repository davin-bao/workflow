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
  public static $rules = array(
    'orders' => 'required|numeric'
  );

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);

    if ( ! static::$app )
      static::$app = app();

    $this->table = static::$app['config']->get('workflow::resourcenode_table');
  }

  public function flow(){
    $this->belongsTo(static::$app['config']->get('workflow::resourceflow'));
  }

  public function user(){
    return $this->belongsTo(static::$app['config']->get('auth.model'));
  }

  public function resourceLog(){
    return $this->hasOne(static::$app['config']->get('workflow::resourcelog'));
  }

  public function recordLog($title, $content){
    if(Config::get('workflow::record_log')){
      $resourceLog = new WorkFlowResourcelog();
      $resourceLog->title = $title;
      $resourceLog->content = $content;
      if($this->resourceLog()->save($resourceLog)){
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
        if($this->resourceLog) {
            $this->resourceLog->delete();
        }
    } catch(Execption $e) {}

    return true;
  }
}