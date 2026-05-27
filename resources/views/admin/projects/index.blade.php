@extends('layouts.admin')

@section('content')
<h1 class="h3 mb-3">{{ __('messages.nav_projects') }}</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.user') }}</th>
                    <th>Status</th>
                    <th>{{ __('messages.due') }}</th>
                    <th>{{ __('messages.nav_tasks') }}</th>
                    <th style="width: 360px;">{{ __('messages.edit') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($projects as $project)
                <tr>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->user?->email }}</td>
                    <td><span class="badge text-bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">{{ $project->status }}</span></td>
                    <td>{{ optional($project->due_date)->toDateString() ?: '-' }}</td>
                    <td>{{ $project->tasks_count }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.projects.update', $project) }}" class="row g-2">
                            @csrf
                            @method('put')
                            <div class="col-12"><input class="form-control form-control-sm" name="name" value="{{ $project->name }}"></div>
                            <div class="col-6">
                                <select class="form-select form-select-sm" name="status">
                                    <option value="active" @selected($project->status === 'active')>active</option>
                                    <option value="archived" @selected($project->status === 'archived')>archived</option>
                                </select>
                            </div>
                            <div class="col-6"><input class="form-control form-control-sm" type="date" min="{{ now()->toDateString() }}" name="due_date" value="{{ optional($project->due_date)->toDateString() }}"></div>
                            <div class="col-12"><textarea class="form-control form-control-sm" name="description" rows="2">{{ $project->description }}</textarea></div>
                            <div class="col-12"><button class="btn btn-primary btn-sm">{{ __('messages.save') }}</button></div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No projects yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $projects->links() }}</div>
@endsection
