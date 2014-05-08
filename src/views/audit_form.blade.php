<div class="box-body">
  <!-- Tabs -->
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab-general" data-toggle="tab">{{{ Lang::get('admin/general.general_info') }}}</a></li>
  </ul>
  <!-- ./ tabs -->

  <!-- Tabs Content -->
  <div class="tab-content">
    <!-- General tab -->
    <div class="tab-pane active" id="tab-general">
      <!-- CSRF Token -->
      <input type="hidden" name="_token" value="{{{ csrf_token() }}}"/>
      <!-- ./ csrf token -->
      <input type="hidden" name="status" value="{{{ $entry->status() }}}"/>
      <br/>
      <!-- comment -->
      <div class="form-group comment">
        <label class="span2 control-label" for="comment">{{{ Lang::get("workflow::workflow.comment") }}}</label>
        <textarea class="form-control" rows="3" name="comment" id="comment"></textarea>
      </div>
      <!-- ./ comment -->

      <!-- result -->
      <div class="form-group result">
        <label class="span2 control-label" for="result" style="width: 100%;">@if($currentNode) {{{ $currentNode->node_name }}}@endif{{{ Lang::get("workflow::workflow.audit_result") }}}</label>

        <!-- Tabs -->
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-agree" data-toggle="tab">{{{ Lang::get("workflow::workflow.agreed") }}}</a></li>
          <li><a href="#tab-disagree" data-toggle="tab">{{{ Lang::get("workflow::workflow.disagreed") }}}</a></li>
        </ul>
        <!-- ./ tabs -->

        <!-- Tabs Content -->
        <div class="tab-content">
          <div class="tab-pane active" id="tab-agree">
            <br/>
            <!-- audit_user -->
            <div class="form-group audit_user @if(count($nextAuditUsers)<=0) hidden @endif">
              <label class="span2 control-label" for="audit_user">{{{ Lang::get('workflow::workflow.audit_user') }}}</label>

              <div class="span6">

                <ul class="list-inline">
                  @foreach ($nextAuditUsers as $user)
                  <li>
                    <div class="checkbox user" data-id="{{{ $user->id }}}">
                      <label><input type="checkbox" style="margin: 0px;" name="audit_users[]" value="{{{ $user->id }}}"> {{{ $user->username }}}</label>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
            <!-- ./ audit_user -->

            <div class="form-group agree-buttons">
              <div class="span6 offset2">
                <button type="submit" class="btn btn-success" name="submit" value="agree">@if($entry->shouldPublish()){{{ Lang::get('workflow::workflow.publish') }}}@else{{{ Lang::get('button.submit') }}}@endif</button>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tab-disagree">
            <br/>

            <div class="form-group disagree-buttons">
              <div class="span6 offset2">
                <button type="submit" class="btn btn-success" name="submit" value="discard">{{{ Lang::get('workflow::workflow.discard') }}}</button>
                <button type="submit" class="btn btn-success" name="submit" value="gofirst">{{{ Lang::get('workflow::workflow.gofirst') }}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ./ general tab -->

  </div>
  <!-- ./ tabs content -->
</div>

{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
  $(function () {

    //$('div.agree-buttons').hide();

    switch ($('input[name="status"]').val()) {
      case 'unstart':
        // push resource user can't comment some words and default agree

        $('#tab-general').append($('div.audit_user'));
        $('#tab-general').append($('div.agree-buttons'));

        $('div.comment').remove();
        $('div.result').remove();

        break;
      case 'proceed':

        break;
      default:
        break;
    }

  });
</script>
@stop