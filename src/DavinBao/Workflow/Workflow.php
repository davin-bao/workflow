<?php namespace DavinBao\Workflow;

use Illuminate\Support\Facades\Facade;

class Workflow
{
    /**
     * Laravel application
     * 
     * @var Illuminate\Foundation\Application
     */
    public $_app;

    /**
     * Create a new confide instance.
     * 
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->_app = $app;
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @access public
     *
     * @return Illuminate\Auth\UserInterface|null
     */
    public function user()
    {
        return $this->_app['auth']->user();
    }


  /**
   * Check whether the controller's action exists.
   * Returns the url if it does. Otherwise false.
   * @param $controllerAction
   * @return string
   */
  public function checkAction( $action, $parameters = array(), $absolute = true )
  {
    try {
      $url = $this->_app['url']->action($action, $parameters, $absolute);
    } catch( InvalidArgumentException $e ) {
      return false;
    }

    return $url;
  }
  /**
   * Display the default create flow view
   *
   * @deprecated
   * @return Illuminate\View\View
   */
  public function makeFlowForm($flow)
  {
    //var_dump($this->_app['config']);exit;
    return $this->_app['view']->make( $this->_app['config']->get('workflow::flow_form'), compact( 'flow') );
  }

}
