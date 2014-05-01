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

        // Creates the resource_flow (Many-to-Many relation) table
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
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Creates the assigned_roles (Many-to-Many relation) table
        Schema::create('resource_log', function($table)
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
        Schema::table('assigned_roles', function(Blueprint $table) {
            $table->dropForeign('assigned_roles_user_id_foreign');
            $table->dropForeign('assigned_roles_role_id_foreign');
        });

        Schema::table('permission_role', function(Blueprint $table) {
            $table->dropForeign('permission_role_permission_id_foreign');
            $table->dropForeign('permission_role_role_id_foreign');
        });

        Schema::drop('assigned_roles');
        Schema::drop('permission_role');
        Schema::drop('roles');
        Schema::drop('permissions');
    }

}
