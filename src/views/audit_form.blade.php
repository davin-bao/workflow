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
                  @if ($nextAuditUsers)
                    @foreach ($nextAuditUsers as $user)
                    <li>
                        <div class="checkbox user" data-id="{{{ $user->id }}}">
                            <label><input type="checkbox" style="margin: 0px;" name="audit_users[]" value="{{{ $user->id }}}"> {{{ $user->username }}}</label>
                        </div>
                    </li>
                    @endforeach
                  @endif
                </ul>
              </div>
            </div>
            <!-- ./ audit_user -->

            <div class="form-group agree-buttons">
              <div class="span6 offset2">
                  @if($entry->shouldPublish())
                    <a href="#sureModal" class="btn btn-success" data-target="#sureModal" data-toggle="modal">{{{ Lang::get('workflow::workflow.publish') }}}</a>
                  @else
                    <button type="submit" class="btn btn-success" name="submit" value="agree">{{{ Lang::get('button.submit') }}}</button>
                  @endif
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


<!-- Modal -->
<div id="sureModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{{{ Lang::get('workflow::workflow.publish') }}}</h3>
            </div>
            <div class="modal-body">
                <p>确认发布该信息吗？</p>
            </div>
            <div class="modal-footer">
                    <!-- CSRF Token -->
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                    <input type="hidden" name="id" value="" />
                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">{{{ Lang::get('button.cancel') }}}</button>
                    <button type="submit" class="ok btn btn-primary" name="submit" value ="agree">{{{ Lang::get('button.ok') }}}</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End -->

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