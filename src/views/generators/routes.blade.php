{{ "\n\n" }}
Route::model('flow', 'Flow');
Route::pattern('flow', '[0-9]+');
{{ "\n" }}
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{
  Route::post('flows/{{ lcfirst(substr($name,0,-10)) }}/createnode', 'AdminFlowController@postCreateNode');
  Route::get('flows/{{ lcfirst(substr($name,0,-10)) }}/edit', 'AdminFlowController@getEdit');
  Route::post('flows/{{ lcfirst(substr($name,0,-10)) }}/edit', 'AdminFlowController@postEdit');
  Route::controller('/flows', 'AdminFlowController');
});
{{ "\n" }}