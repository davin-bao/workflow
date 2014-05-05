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

class WorkFlowFlow extends Ardent
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
    'flow_name' => 'required|between:4,128'
  );
  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);

    if ( ! static::$app )
      static::$app = app();

    $this->table = static::$app['config']->get('workflow::flows_table');
  }

  /**
   * One-to-Many relations with Nodes
   */
  public function nodes()
  {
    return $this->hasMany(static::$app['config']->get('workflow::node'));
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function flowable()
  {
    return $this->morphTo();
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
      \DB::table(static::$app['config']->get('workflow::nodes_table'))->where('flow_id', $this->id)->delete();
    } catch(Execption $e) {}

    return true;
  }
}