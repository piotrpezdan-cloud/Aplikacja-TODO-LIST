@extends('layouts.admin')
@section('content')
<h4>{{ __('messages.nav_categories') }}</h4>
<form class="row g-2 mb-3" method="post" action="{{ route('admin.categories.store') }}">@csrf
<div class="col"><input class="form-control" name="name" placeholder="{{ __('messages.name') }}" required></div>
<div class="col"><input class="form-control" name="slug" placeholder="Slug" required></div>
<div class="col"><input class="form-control" name="color" value="#3b82f6" required></div>
<div class="col"><button class="btn btn-primary">{{ __('messages.add') }}</button></div>
</form>
<table class="table"><thead><tr><th>{{ __('messages.name') }}</th><th>Slug</th><th>{{ __('messages.color') }}</th><th>{{ __('messages.actions') }}</th></tr></thead><tbody>
@foreach($categories as $category)
<tr>
<td>{{ $category->name }}</td><td>{{ $category->slug }}</td><td><span class="badge" style="background:{{ $category->color }}">{{ $category->color }}</span></td>
<td>
<form method="post" action="{{ route('admin.categories.update',$category) }}" class="d-inline">@csrf @method('put')
<input type="hidden" name="name" value="{{ $category->name }}"><input type="hidden" name="slug" value="{{ $category->slug }}"><input type="hidden" name="color" value="{{ $category->color }}"><button class="btn btn-sm btn-outline-secondary">{{ __('messages.save') }}</button></form>
<button class="btn btn-sm btn-danger" onclick="removeCategory({{ $category->id }})">{{ __('messages.delete') }}</button>
</td>
</tr>
@endforeach
</tbody></table>{{ $categories->links() }}
<script>
function removeCategory(id){if(confirm('{{ __('messages.delete_confirm') }}')) fetch(`/admin/categories/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}}).then(()=>location.reload())}
</script>
@endsection
