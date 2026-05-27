@extends('layouts.admin')
@section('content')
<h4>{{ __('messages.send_email') }}</h4>
<form method="post" action="{{ route('admin.mail.send') }}">@csrf
<select class="form-select mb-2" name="user_id" required>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->email }}</option>@endforeach</select>
<input class="form-control mb-2" name="subject" placeholder="{{ __('messages.subject') }}" required>
<textarea class="form-control mb-2" name="message" rows="6" required></textarea>
<button class="btn btn-primary">{{ __('messages.send') }}</button>
</form>
@endsection
