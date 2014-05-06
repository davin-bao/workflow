<!-- select_flow -->
<div class="form-group {{{ $errors->has('flow_id') || $errors->has('flow_id') ? 'has-error' : '' }}}">
  <label class="span2 control-label" for="flow_id">{{{ Lang::get('workflow::workflow.flow') }}}</label>
  <div class="span6">
    <select class="form-control" name="flow_id" id="flow_id">
      @foreach ($flows as $flow)
      <option value="{{{ $flow->id }}}">{{{ $flow->flow_name }}}</option>
      @endforeach
    </select>
    {{ $errors->first('flow_id', '<label class="control-label" for="flow_id"><i class="fa fa-times-circle-o"></i> :message</label>') }}
  </div>
</div>
<!-- ./ select_flow -->