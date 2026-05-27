@extends('layouts.admin')
@section('content')
<h4>{{ __('messages.nav_audit_logs') }}</h4>
<table class="table table-sm"><thead><tr><th>{{ __('messages.date') }}</th><th>Admin</th><th>{{ __('messages.action') }}</th><th>{{ __('messages.entity') }}</th><th>{{ __('messages.description') }}</th></tr></thead><tbody>
@foreach($logs as $log)
<tr><td>{{ $log->created_at }}</td><td>{{ $log->user?->email }}</td><td>{{ $log->action }}</td><td>{{ $log->entity_type }}#{{ $log->entity_id }}</td><td>{{ $log->description }}</td></tr>
@endforeach
</tbody></table>{{ $logs->links() }}
@endsection
