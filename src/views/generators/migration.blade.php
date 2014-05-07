{{ '<?php' }}

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class WorkflowSetupTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Creates the flows table
        Schema::create('flows', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('flow_name')->unique();
            $table->enum('resource_type', array({{ $res_type }}));
            $table->timestamps();
        });

        // Creates the nodes table
        Schema::create('nodes', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('node_name');
            $table->integer('flow_id')->unsigned();
            $table->integer('orders')->unsigned();
            $table->timestamps();
            $table->foreign('flow_id')->references('id')->on('flows');
        });

        // Creates the node_role (Many-to-Many relation) table
        Schema::create('node_role', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->foreign('node_id')->references('id')->on('nodes');
            $table->foreign('role_id')->references('id')->on('roles'); // assumes a roles table
        });

        // Creates the node_user (Many-to-Many relation) table
        Schema::create('node_user', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('node_id')->references('id')->on('nodes');
            $table->foreign('user_id')->references('id')->on('users'); // assumes a users table
        });

        // Creates the resource_flows (Many-to-Many relation) table
        Schema::create('resourceflow', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resource_id')->unsigned();
            $table->integer('flow_id')->unsigned();
            $table->enum('status', array('unstart','discard', 'proceed', 'completed', 'archived'))->default('unstart');
            $table->integer('node_orders')->unsigned()->default(0);
            $table->timestamps();
            $table->foreign('flow_id')->references('id')->on('flows');
        });

        // Creates the resource_node (Many-to-Many relation) table
        Schema::create('resourcenode', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resourceflow_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('orders')->unsigned();
            $table->text('comment');
            $table->enum('result', array('agreed', 'disagreed', 'unaudited'))->default('unaudited');
            $table->timestamps();
            $table->foreign('resourceflow_id')->references('id')->on('resourceflow');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Creates the resourcelog (One-to-Many relation) table
        Schema::create('resourcelog', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resourcenode_id')->unsigned();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
            $table->foreign('resourcenode_id')->references('id')->on('resourcenode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nodes', function(Blueprint $table) {
          $table->dropForeign('nodes_flow_id_foreign');
        });
        Schema::table('node_role', function(Blueprint $table) {
          $table->dropForeign('node_role_node_id_foreign');
          $table->dropForeign('node_role_role_id_foreign');
        });
        Schema::table('node_user', function(Blueprint $table) {
            $table->dropForeign('node_user_node_id_foreign');
            $table->dropForeign('node_user_user_id_foreign');
        });
        Schema::table('resourceflow', function(Blueprint $table) {
          $table->dropForeign('resourceflow_flow_id_foreign');
        });
        Schema::table('resourcenode', function(Blueprint $table) {
          $table->dropForeign('resourcenode_resourceflow_id_foreign');
          $table->dropForeign('resourcenode_user_id_foreign');
        });
        Schema::table('resourcelog', function(Blueprint $table) {
          $table->dropForeign('resourcelog_resourcenode_id_foreign');
        });

        Schema::drop('flows');
        Schema::drop('nodes');
        Schema::drop('node_role');
        Schema::drop('node_user');
        Schema::drop('resourceflow');
        Schema::drop('resourcenode');
        Schema::drop('resourcelog');
    }

}
