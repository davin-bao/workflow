
@section('styles')
.btn.btn-circle {
  width: 40px;
  height: 40px;
  line-height: 40px;
  padding: 0;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  border-radius: 50%;
}
@stop

<div class="flow-graph">
  <button class="btn btn-circle btn-success">{{{ Lang::get("workflow::workflow.start") }}}</button><i class="fa fa-long-arrow-right"></i>
  @if ($flow)
  @foreach ($flow->nodes as $node)
  <button class="btn btn-circle @if ($node->orders<$orderID) btn-success @elseif($node->orders==$orderID && $status != 'discard') btn-warning @else btn-danger @endif"
          data-toggle="tooltip"
          data-original-title="{{{ $node->node_name }}}">
    {{{ $node->node_name }}}
  </button>
  <i class="fa fa-long-arrow-right"></i>
  @endforeach
  @endif
  <button class="btn btn-circle @if ($flow && count($flow->nodes)<$orderID) btn-success @elseif($status == 'discard') btn-danger @endif">{{{ Lang::get("workflow::workflow.stop") }}}</button>
</div>
