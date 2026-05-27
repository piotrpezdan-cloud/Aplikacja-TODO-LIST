@extends('layouts.admin')
@section('content')
<div class="row g-3">
    @foreach($stats as $label => $value)
        <div class="col-md-3"><div class="card"><div class="card-body"><h6>{{ __('messages.stat_'.$label) }}</h6><h3>{{ $value }}</h3></div></div></div>
    @endforeach
</div>
@endsection
