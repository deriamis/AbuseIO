@extends('app')

@section('extrajs')
    <script src="{{ asset('/js/tickets.js') }}"></script>
@stop

@section('content')
    <h1 class="page-header">{{ trans('tickets.headers.detail') }}: {{ $ticket->id }} - {{ trans('tickets.evidence') }}: {{ $evidenceId }}</h1>

    @if(is_object($evidence))
    <dl class="dl-horizontal">
        @foreach (['from', 'to', 'cc', 'subject'] as $header)
            @if(!empty($evidence->getHeader($header)))
                <dt>{{ ucfirst($header) }} :</dt>
                <dd>{{ $evidence->getHeader($header) }}</dd>
            @endif
        @endforeach

        @if (count($evidence->getAttachments()) > 0)
            <dt>Attachments :</dt>
            <dd>
                <table class="table table-condensed">
                    @foreach ($evidence->getAttachments() as $attachment)
                        <tr>
                            <td><a href='{{ Request::url() }}/attachment/{{ $attachment->getFilename() }}'>{{ $attachment->getFilename() }}</a></td>
                            <td>{{ filesize($evidenceTempDir.$attachment->getFilename()) }} bytes</td>
                            <td>{{ $attachment->getContentType() }}</td>
                        </tr>
                    @endforeach
                </table>
            </dd>
        @endif

            <dt>Message :</dt>
            <dd><pre>{{ $evidence->getMessageBody('text') }}</pre></dd>
    </dl>
    @else
        <dl class="dl-horizontal">
        @foreach (['from', 'to', 'cc', 'subject'] as $header)
            @if(!empty($evidence[$header]))
                <dt>{{ ucfirst($header) }} :</dt>
                <dd>{{ $evidence[$header] }}</dd>
             @endif
        @endforeach

            @if (count($evidence['message']->attachments) > 0)
                <dt>Attachments :</dt>
                <dd>
                    <table class="table table-condensed">
                        @foreach ($evidence['message']->attachments as $attachment)
                            <tr>
                                <td><a href='{{ Request::url() }}/attachment/{{ $attachment->filename }}'>{{ $attachment->filename }}</a></td>
                                <td>{{ $attachment->size }} bytes</td>
                                <td>{{ $attachment->contentType }}</td>
                            </tr>
                        @endforeach
                    </table>
                </dd>
            @endif

            <dt>Message :</dt>
            <dd>
                This incident was created by an CLI, GUI or API command. The received payload is displayed below:
                <pre>{{ print_r($evidence['message'], true) }}</pre>
            </dd>
        </dl>
    @endif
@endsection