<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-5-3
 * Time: AM 11:08
 */

 namespace DavinBao\Workflow;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Config;

trait HasNodeForUser
{
    /**
     * Many-to-Many relations with Node
     */
    public function nodes()
    {
        return $this->belongsToMany(Config::get('workflow::node'), Config::get('workflow::node_user_table'));
    }




}