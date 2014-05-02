
{{-- styles --}}
@section('styles')
@stop

<br/>
<div id="message" class="alert alert-success alert-block alert-dismissable hidden">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <h4></h4>
  <p></p>
</div>
<form method="post" autocomplete="off">

        <div class="box-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-general" data-toggle="tab">{{{ Lang::get('workflow::workflow.general_info') }}}</a></li>
            </ul>
            <!-- ./ tabs -->

            <!-- Tabs Content -->
            <div class="tab-content">
                <!-- General tab -->
                <div class="tab-pane active" id="tab-general">
                    <!-- CSRF Token -->
                  <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                  <input type="hidden" name="flow_id" value="@if (isset($flow)){{ $flow->id }}@endif" />
                    <!-- ./ csrf token -->
                    <br/>
                    <!-- info_name -->
                    <div class="form-group">
                        <label class="span2 control-label" for="flow_name">{{{ Lang::get('workflow::workflow.flow_name') }}}</label>
                        <div class="span6">
                          <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="flow_name" style="margin: 0px;" id="flow_name" value="{{{ Input::old('flow_name', isset($flow) ? $flow->flow_name : null) }}}">
                            <span class="input-group-btn">
                              <button data-url="@if(isset($flow)){{ URL::to('admin/flows/' . $flow->id . '/edit') }}@else{{{ URL::to('admin/flows/create') }}}?_token={{{ csrf_token() }}}@endif"
                                      class="btn btn-primary btn-flat" name="flow_save" type="button"><i class="fa fa-save"></i> {{{ Lang::get('workflow::button.save') }}}</button>
                            </span>
                          </div>
                            {{ $errors->first('flow_name', '<label class="control-label" for="flow_name"><i class="fa fa-times-circle-o"></i> :message</label>') }}
                        </div>
                    </div>
                    <!-- ./ info_name -->
                  <div class="form-group">
                    <label class="span2 control-label" for="node">{{{ Lang::get('workflow::workflow.node') }}}</label>
                    &nbsp;<a href="#" id="node-add"><i class="fa fa-plus"></i></a>
                    <div class="span6">

                      <ul class="todo-list ui-sortable">
                      </ul>

                    </div>
                  </div>

                </div>
                <!-- ./ general tab -->

            </div>
            <!-- ./ tabs content -->

            <!-- Form Actions -->
            <div class="form-group">
                <div class="span6 offset2">
                    <a type="reset" class="btn btn-default" href="{{{ URL::to('admin/flows') }}}">{{{ Lang::get('workflow::button.return') }}}</a>
                    <button type="submit" class="btn btn-success">{{{ Lang::get('workflow::button.submit') }}}</button>
                </div>
            </div>
            <!-- ./ form actions -->
        </div>
    </form>


{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
  $(function () {

    $('.todo-list').sortable();

    $('input[name="flow_name"]').focusout(function(){
      $('button[name="flow_save"]').removeClass('disabled').html('<i class="fa fa-save"></i> {{{ Lang::get("workflow::button.save") }}}');
    });

    $('button[name="flow_save"]').click(function(){
      var flow_name = $('input[name="flow_name"]').val();
      var saveUrl = $(this).attr('data-url');
      $.ajax({
        url: saveUrl,
        data: { flow_name: flow_name },
        type: 'POST',
        dataType : "json"
      }).done(function( data ) {
        if(data.result){
          $('button[name="flow_save"]').removeClass('disabled')
            .addClass('disabled')
            .html('<i class="fa fa-check"></i>'+data.message);
          $('button[name="flow_save"]').attr('data-url', "{{ URL::to('admin/flows') }}/"+ data.id  + "/edit");
          $('input[name="flow_id"]').val(data.id);
          showSuccessMsg(data.message);
        }else{
          showErrorMsg(data.message);
        }
      });
    });

    $('#node-add').click(function(){
        $('.todo-list').append(getNodeLoadingList());
      var flowId = $('input[name="flow_id"]').val();
      var saveUrl = "{{{ URL::to('admin/flows/') }}}/"+flowId+"/createnode?_token={{{ csrf_token() }}}";
      $.ajax({
        url: saveUrl,
        type: 'POST',
        dataType : "json"
      }).done(function( data ) {
        if(data.result){
            $('.todo-list .loading').remove();
          $('.todo-list').append(getNodeList(data.id, data.node_name, data.users, data.roles));
          addModifyNodeEvent(data.id);
          //showSuccessMsg(data.message);
        }else{
          showErrorMsg(data.message);
        }
      });
    });
    function addModifyNodeEvent(id){
      $(".todo-list .tools .fa-edit").click(function(){
        var parent = $(this).parents('.row[data-id="'+id+'"]');
        var nodeName = parent.find('.node_name');
        var nodeUser = parent.find('.node_user');
        var nodeRole = parent.find('.node_role');
        var nodeTool = parent.find('.tools');

        nodeName.after('<input name="node_name" value="'+nodeName.html()+'"/>').show();
        nodeName.hide();
        nodeUser.after('<input name="node_user" value="'+nodeName.html()+'"/>').show();
        nodeUser.hide();
        nodeRole.after('<input name="node_role" value="'+nodeName.html()+'"/>').show();
        nodeRole.hide();
        nodeTool.after('<a name="node-save" class="btn btn-primary"><i class="fa fa-save"></i> {{{ Lang::get("workflow::button.save") }}}</a>').show();
        nodeTool.hide();

        addModifyNodeAjaxEvent();
      });
    }

    function addModifyNodeAjaxEvent(){
      $('input[name="node_name"').focusout(function(){
        $(this).after('<a class="node_name" href="#">'+$(this).val()+'</a>').show();
        $(this).hide();
        addModifyNodeEvent();
      });
    }


    function getNodeLoadingList(){
        return '<li class="loading"><i class="ion-loading-c"></i></li>';
    }

    function getNodeList(id, name, users, roles){
      return '<li>\
        <div class="row" data-id="'+id+'">\
        <div class="col-xs-3">\
        <span class="handle">\
        <i class="fa fa-ellipsis-v"></i>\
        <i class="fa fa-ellipsis-v"></i>\
        </span><span class="node_name">'+name+'</span>\
        </div>\
        <div class="col-xs-4"><i class="fa fa-user"></i><span class="node_user">'+users+'</span></div>\
        <div class="col-xs-4"><i class="fa fa-group"></i><span class="node_role">'+roles+'</span></div>\
        <div class="col-xs-1">\
          <div class="tools">\
          <i class="fa fa-edit"></i>\
          <i class="fa fa-trash-o"></i>\
        </div>\
        </div>\
        </div>\
        </li>';
    }

    function showSuccessMsg(msg){
      $('#message').removeClass('hidden')
        .removeClass('alert-danger')
        .removeClass('alert-success')
        .addClass('alert-success');
      $('#message button').before('<i class="fa fa-check"></i>');
      $('#message h4').html("{{ Lang::get('general.success') }}");
      $('#message p').html(msg);
    }
    function showErrorMsg(msg){
      $('#message').removeClass('hidden')
        .removeClass('alert-danger')
        .removeClass('alert-success')
        .addClass('alert-danger');
      $('#message button').before('<i class="fa fa-ban"></i>');
      $('#message h4').html("{{ Lang::get('general.error') }}");
      $('#message p').html(msg);
    }

  });
</script>
@stop