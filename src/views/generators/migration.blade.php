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
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Creates the nodes table
        Schema::create('nodes', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->integer('flow_id')->unsigned();
            $table->integer('order')->unsigned();
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
        Schema::create('resource_flow', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resource_id')->unsigned();
            $table->integer('flow_id')->unsigned();
            $table->integer('status')->unsigned();      //status has 3 state
            $table->integer('node_order')->unsigned();
            $table->foreign('flow_id')->references('id')->on('flows');
        });

        // Creates the resource_node (Many-to-Many relation) table
        Schema::create('resource_node', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resource_flow_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('order')->unsigned();
            $table->text('comment');
            $table->boolean('result');
            $table->foreign('resource_flow_id')->references('id')->on('resource_flow');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Creates the resource_logs (One-to-Many relation) table
        Schema::create('resource_logs', function($table)
        {
            $table->increments('id')->unsigned();
            $table->integer('resource_node_id')->unsigned();
            $table->string('title');
            $table->text('content');
            $table->foreign('resource_node_id')->references('id')->on('resource_node');
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
        Schema::table('resource_flow', function(Blueprint $table) {
          $table->dropForeign('resource_flow_flow_id_foreign');
        });
        Schema::table('resource_node', function(Blueprint $table) {
          $table->dropForeign('resource_node_resource_flow_id_foreign');
          $table->dropForeign('resource_node_user_id_foreign');
        });
        Schema::table('resource_logs', function(Blueprint $table) {
          $table->dropForeign('resource_logs_resource_node_id_foreign');
        });

        Schema::drop('flows');
        Schema::drop('nodes');
        Schema::drop('node_role');
        Schema::drop('node_user');
        Schema::drop('resource_flow');
        Schema::drop('resource_node');
        Schema::drop('resource_logs');
    }

}
