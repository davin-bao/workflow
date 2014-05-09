<br/>
<!-- row -->
<div class="row">
  <div class="col-md-12">
    @if($entry->getAuditByTimeLine())
    <!-- The time line -->
    <ul class="timeline">
      @foreach ( $entry->getAuditByTimeLine() as $key => $items )
      <!-- timeline time label -->
      <li class="time-label">
        <span class="bg-red">{{{ $key }}}</span>
      </li>
      <!-- /.timeline-label -->
      @foreach ( $items as $ikey => $item )
      <!-- timeline item -->
      <li>
        @if ($item['result'] == 'agreed')
        <i class="fa fa-comments bg-green"></i>
        @elseif($item['result'] == 'disagreed')
        <i class="fa fa-ban bg-red"></i>
        @else
        <i class="fa fa-comments bg-yellow"></i>
        @endif
        <div class="timeline-item">
          <span class="time"><i class="fa fa-clock-o"></i>{{{ $ikey }}}</span>

          <h3 class="timeline-header"><a href="#">{{{ $item['username'] }}}</a> {{{ $item['nodename'] }}}
            @if ($item['result'] == 'agreed')
            <span class="label label-success">
            @elseif($item['result'] == 'disagreed')
            <span class="label label-danger">
            @else
            <span class="label label-warning">
            @endif
            {{{ Lang::get('workflow::workflow.'.$item['result']) }}}</span></h3>

          <div class="timeline-body">

           &nbsp;&nbsp;&nbsp;&nbsp;{{{ $item['comment'] }}}
          </div>
          <div class='timeline-footer'>
            <a class="btn btn-primary btn-xs" href="{{{ URL::to(sprintf('admin/nodes/showlog?id=%d', $item['id'])) }}}">{{{ Lang::get('workflow::workflow.show_log') }}}</a>
          </div>
        </div>
      </li>
      <!-- END timeline item -->

      @endforeach
      @endforeach
      <li>
        <i class="fa fa-clock-o"></i>
      </li>
    </ul>
    @endif
  </div>
  <!-- /.col -->
</div><!-- /.row -->