@extends('layouts.admin')
@section('content')
<h4>{{ __('messages.nav_users') }}</h4>
<table class="table table-striped"><thead><tr><th>ID</th><th>Email</th><th>{{ __('messages.name') }}</th><th>{{ __('messages.active') }}</th><th></th></tr></thead><tbody>
@foreach($users as $user)
<tr>
<td>{{ $user->id }}</td><td>{{ $user->email }}</td><td>{{ $user->first_name }} {{ $user->last_name }}</td>
<td>
@if($user->email === 'admin@todo-list.local')
    <button class="btn btn-sm btn-success" disabled title="{{ __('messages.system_admin_cannot_be_deactivated') }}">{{ __('messages.yes') }}</button>
@else
    <button class="btn btn-sm {{ $user->is_active ? 'btn-success':'btn-secondary' }}" onclick="toggleUser({{ $user->id }})">{{ $user->is_active ? __('messages.yes'):__('messages.no') }}</button>
@endif
</td>
<td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit',$user) }}">{{ __('messages.edit') }}</a></td>
</tr>
@endforeach
</tbody></table>
{{ $users->links() }}
<script>
function toggleUser(id){patch(`/admin/users/${id}/toggle-active`).then((response)=>{if(response.ok){location.reload()}})}
</script>
@endsection
