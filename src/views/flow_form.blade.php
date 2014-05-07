
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
                  <!-- flow_name -->
                  <div class="form-group">
                    <label class="span2 control-label" for="flow_name">{{{ Lang::get('workflow::workflow.flow') }}} {{{ Lang::get('workflow::workflow.name') }}}</label>
                    <input type="text" class="form-control" name="flow_name" style="margin: 0px;" id="flow_name" value="{{{ Input::old('flow_name', isset($flow) ? $flow->flow_name : null) }}}">
                    {{ $errors->first('flow_name', '<label class="control-label" for="flow_name"><i class="fa fa-times-circle-o"></i> :message</label>') }}
                  </div>
                  <!-- ./ flow_name -->
                  <!-- resource_type -->
                  <div class="form-group {{{ $errors->has('resource_type') || $errors->has('resource_type') ? 'has-error' : '' }}}">
                    <label class="span2 control-label" for="resource_type">{{{ Lang::get('workflow::workflow.resource_type') }}}</label>
                    <div class="span6">
                      <select class="form-control" name="resource_type" id="resource_type">
                      @if (!$flow)
                        @foreach (Config::get('workflow::resource_type') as $type)
                        <option value="{{{ str_replace('"','',$type) }}}"{{{ (Input::old('resource_type') === str_replace('"','',$type) ? ' selected="selected"' : '') }}}>{{{ Lang::get('admin/menu.'.str_replace('"','',$type)) }}}</option>
                        @endforeach
                      @else
                        @foreach (Config::get('workflow::resource_type') as $type)
                        <option value="{{{ str_replace('"','',$type) }}}"{{{ ($flow->resource_type === str_replace('"','',$type) ? ' selected="selected"' : '') }}}>{{{ Lang::get('admin/menu.'.str_replace('"','',$type)) }}}</option>
                        @endforeach
                      @endif
                      </select>
                      {{ $errors->first('resource_type', '<label class="control-label" for="resource_type"><i class="fa fa-times-circle-o"></i> :message</label>') }}
                    </div>
                  </div>
                  <!-- ./ resource_type -->

                  <div class="form-group @if(!$flow) hidden @endif">
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
                    <button data-url="@if(isset($flow)){{ URL::to('admin/flows/' . $flow->id . '/edit') }}@else{{{ URL::to('admin/flows/create') }}}?_token={{{ csrf_token() }}}@endif"
                                                   class="btn btn-primary" name="flow_save" type="button"><i class="fa fa-save"></i> {{{ Lang::get('workflow::button.save') }}}</button>
                    <a type="reset" class="btn btn-default" href="{{{ URL::to('admin/flows') }}}">{{{ Lang::get('workflow::button.return') }}}</a>
                </div>
            </div>
            <!-- ./ form actions -->
        </div>
    </form>


<!-- Modal -->
<div class="modal fade" id="newModal" tabindex="-1" role="dialog" aria-labelledby="newModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><i class="ion ion-loading-c"></i></h4>
            </div>
            <div class="modal-body">

                <div id="node-message" class="alert alert-success alert-block alert-dismissable hidden">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4></h4>
                    <p></p>
                </div>

                <div class="text-center"></div>
                <div class="node-form">
                    <div class="form-group">
                        <label class="span2 control-label" for="node_name">{{{ Lang::get("workflow::workflow.name") }}}</label>
                        <input type="hidden" name="node_id" id="node_name" value="">
                        <input type="text" class="form-control" name="node_name" id="node_name" value="">
                    </div>
                    <div class="form-group">
                        <label class="span2 control-label" for="node_name">{{{ Lang::get("workflow::workflow.follower") }}}</label>
                        <ul class="nav nav-tabs">
                            @foreach ($roles as $role)
                            <li><a href="#{{{ $role->name }}}" data-toggle="tab">{{{ $role->name }}}</a></li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach ($roles as $role)
                            <div class="tab-pane" id="{{{ $role->name }}}">
                                <div class="checkbox role" data-id="{{{ $role->id }}}" id="{{{ $role->name }}}">
                                    <label>
                                        <input type="checkbox" style="margin: 0px;" value="">{{{ Lang::get("workflow::workflow.select_this_role") }}}</label>
                                </div>
                                <ul class="list-inline">
                                    @foreach ($role->users as $user)
                                    <li>
                                        <div class="checkbox user" data-id="{{{ $user->id }}}">
                                            <label><input type="checkbox" style="margin: 0px;" value=""> {{{ $user->username }}}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-flat" name="node_save" type="button"><i class="fa fa-save"></i> {{{ Lang::get('workflow::button.save') }}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
$(function () {

@if ($flow)
    @foreach ($flow->nodes as $node)
        $('.todo-list').append(getNodeList('{{  $node->id }}','{{  $node->node_name }}','{{  $node->UsersString() }}','{{  $node->RolesString() }}'));
        addModifyNodeEvent({{  $node->id }});
    @endforeach
@endif

$('.todo-list').sortable({
    change: function( event, ui ) {
        $('button[name="flow_save"]').removeClass('disabled').html('<i class="fa fa-save"></i> {{{ Lang::get("workflow::button.save") }}}');
    }
});

$('input[name="flow_name"]').focusout(function(){
    $('button[name="flow_save"]').removeClass('disabled').html('<i class="fa fa-save"></i> {{{ Lang::get("workflow::button.save") }}}');
});

$('button[name="flow_save"]').click(function(){
    var node_ids = [];
    $('.todo-list li').each(function(){
        node_ids.push($(this).find('div').attr('data-id'));
    });
    console.log(node_ids);
    var flow_name = $('input[name="flow_name"]').val();
    var resource_type = $('select[name="resource_type"]').val();
    var saveUrl = $(this).attr('data-url');
    $.ajax({
        url: saveUrl,
        data: { flow_name: flow_name,resource_type: resource_type, node_ids: node_ids },
        type: 'POST',
        dataType : "json"
    }).done(function( data ) {
        if(data.result){
            $('button[name="flow_save"]').removeClass('disabled')
                .addClass('disabled')
                .html('<i class="fa fa-check"></i>'+data.message);
            $('button[name="flow_save"]').attr('data-url', "{{ URL::to('admin/flows') }}/"+ data.id  + "/edit");
            $('input[name="flow_id"]').val(data.id);
            $('.tab-content label[for="node"]').parent().removeClass('hidden').fadeIn();
            showSuccessMsg(data.message);
        }else{
            showErrorMsg(data.message);
        }
    });
});

$('#node-add').click(function(){
    $('.todo-list .loading').remove();
    $('.todo-list').append(getNodeLoadingList());
    var flowId = $('input[name="flow_id"]').val();

    createNode(flowId, function( data ) {
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
$('#newModal').on('hidden.bs.modal', function (e) {
  $('.todo-list li .row').show();
  $('.todo-list .loading').hide();
})

function addModifyNodeEvent(id){
    $(".todo-list .row[data-id='"+id+"'] .tools .fa-edit").click(function(){
        showModal();
        var parent = $(this).parents('.row[data-id="'+id+'"]').hide();
        parent.after(getNodeLoadingList());

        getNode(id, function(data) {
            if(data.result){
                showEditNodeForm(data);
                showModalTools(data.id);
            }else{
                showErrorMsg(data.message);
            }
        });

    });
    $(".todo-list .row[data-id='"+id+"'] .tools .fa-trash-o").click(function(){
        var parent = $(this).parents('.row[data-id="'+id+'"]').hide();
        parent.after(getNodeLoadingList());

        removeNode(id, function(data) {
            if(data.result){
                parent.parent('li').remove();
            }else{
                showErrorMsg(data.message);
            }
        });

    });
}

function getNode(id, callback){
    var getUrl = "{{{ URL::to('admin/nodes/') }}}/"+id+"/edit?_token={{{ csrf_token() }}}";
    $.ajax({
        url: getUrl,
        type: 'GET',
        dataType : "json"
    }).done(callback);
}

function removeNode(id, callback){
    var getUrl = "{{{ URL::to('admin/nodes/') }}}/"+id+"/delete?_token={{{ csrf_token() }}}";
    $.ajax({
        url: getUrl,
        type: 'DELETE',
        dataType : "json"
    }).done(callback);
}

function createNode(id,callback){
    var saveUrl = "{{{ URL::to('admin/flows/') }}}/"+id+"/createnode?_token={{{ csrf_token() }}}";
    $.ajax({
        url: saveUrl,
        type: 'POST',
        dataType : "json"
    }).done(callback);
}

function saveNode(id){
    var getUrl = "{{{ URL::to('admin/nodes/') }}}/"+id+"/edit?_token={{{ csrf_token() }}}";
    var node_name = $('#newModal input[name="node_name"]').val();
    var role_ids = [];
    var user_ids = [];
    $('#newModal .tab-pane .role').each(function(){
        if($(this).find('.icheckbox_minimal').attr('aria-checked') == 'true'){
            role_ids.push($(this).attr('data-id'));
        }
    });
    $('#newModal .tab-pane .user').each(function(){
        if($(this).find('.icheckbox_minimal').attr('aria-checked') == 'true'){
            user_ids.push($(this).attr('data-id'));
        }
    });

    var data = { node_name: node_name, roles:role_ids, users:user_ids };
    $.ajax({
        url: getUrl,
        type: 'POST',
        dataType : "json",
        data: data
    }).done(function( data ) {
        if(data.result){
            $('#newModal').modal('hide');
            getNode(id, function(data) {
                if(data.result){
                    $('.todo-list .loading').parent('li').after(getNodeList(data.id, data.node_name, data.userstr, data.rolestr));
                    $('.todo-list .loading').parent('li').remove();
                    addModifyNodeEvent(data.id);
                }else{
                    showModalErrorMsg(data.message);
                }
            });
        }else{
            showModalTools(data.id);
            showModalErrorMsg(data.message);
        }
    });
}

function getNodeLoadingList(){
    return '<div class="loading"><i class="ion ion-loading-c"></i></div>';
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

function showEditNodeForm(data){
    $('#newModal .icheckbox_minimal').removeClass('checked').attr('aria-checked','false');
    $('#newModal input[name="node_name"]').val(data.node_name);

    for(var i=0;i<data.roles.length;i++) {
        $('#newModal .tab-pane .role[id="'+data.roles[i].name+'"] .icheckbox_minimal').addClass('checked').attr('aria-checked','true');
    }
    for(var i=0;i<data.users.length;i++){
        $('#newModal .list-inline .user[data-id="'+data.users[i].id+'"] .icheckbox_minimal').addClass('checked').attr('aria-checked','true');
    }

  $('#newModal .nav-tabs li').removeClass('active').first().addClass('active');
  $('#newModal .tab-content .tab-pane').removeClass('active').first().addClass('active');
}

function showModal(){
    $("#newModal .modal-title").html('{{ Lang::get("workflow::workflow.edit") }}{{ Lang::get("workflow::workflow.node") }}');
    hideModalTools();
    $("#newModal").modal({
        backdrop:true,
        show: true
    });
}

function hideModalTools(){
    $("#newModal .modal-footer").html('<div class="text-center"><i class="ion ion-loading-c"></i></div>');
}

function showModalTools(id){
    $("#newModal .modal-footer").html('<button class="btn btn-primary btn-flat" name="node_save" type="button"><i class="fa fa-save"></i> {{{ Lang::get("workflow::button.save") }}}</button>\
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
    $("#newModal .modal-footer button[name='node_save']").click(function(){
        hideModalTools();
        saveNode(id);
    });
//    $("#newModal .modal-footer button[data-dismiss='modal']").click(function(){
//        $('.todo-list li .row').show();
//        $('.todo-list .loading').remove();
//    });
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

function showModalErrorMsg(msg){
    $('#node-message').removeClass('hidden')
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