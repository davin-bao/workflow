<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: PM 2:13
 */
namespace DavinBao\Workflow;

use Illuminate\Database\Eloquent\Model;
use LaravelBook\Ardent\Ardent;
use Config;

class WorkFlowResourcelog extends Ardent
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
  public static $rules = array();

  /**
   * Creates a new instance of the model
   */
  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
    $this->table = Config::get('workflow::resourcelog_table');
  }

  public function resourcenode(){
    return $this->hasOne('WorkFlowResourcenode');
  }


}