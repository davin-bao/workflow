<?php
/**
 * Created by PhpStorm.
 * User: davin.bao
 * Date: 14-5-5
 * Time: PM 4:19
 */
namespace DavinBao\Workflow;

use Mockery as m;

class HasFlowForResourceTest extends \Orchestra\Testbench\TestCase {

  protected $testingModel;

  public function tearDown(){
    m::close();
  }

  public function setUp()
  {
    WorkFlowResourceflow::$app = $this->mockApp();

    $this->testingModel = new TestingModel();
  }


  public function testBindingFlow()
  {
    $flow = m::mock('WorkflowFlow');
    $this->testingModel->bindingFlow(1);
    $this->assertEquals(1,1);
  }

  private function mockApp()
  {
    // Mocks the application components
    $app = array();

    $app['config'] = m::mock( 'Config' );
    $app['config']->shouldReceive( 'get' )
      ->with( 'auth.table' )
      ->andReturn( 'users' );

    $app['config']->shouldReceive( 'workflow::resource_flow_table' )
      ->andReturn( 'resource_flow' );
    return $app;
  }


  protected function getPackageProviders()
  {
    return array('DavinBao\WorkflowServiceProvider');
  }

  protected function getPackageAliases()
  {
    return array(
      'DavinBao' => 'DavinBao\Facade'
    );
  }
}

class TestingModel
{
  use HasFlowForResource;

  public $roles = array();
  public $perms = array();

  function __construct()
  {

  }
}