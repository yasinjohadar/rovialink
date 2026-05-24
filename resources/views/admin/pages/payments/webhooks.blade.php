@extends('admin.layouts.master')

@section('page-title')
    Webhooks
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <h4 class="my-4">سجل Webhooks</h4>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Provider</th><th>Type</th><th>Status</th><th>At</th></tr></thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event->provider }}</td>
                                <td>{{ $event->event_type }}</td>
                                <td>{{ $event->status }}</td>
                                <td>{{ $event->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $events->links() }}
            </div>
        </div>
    </div>
@endsection
