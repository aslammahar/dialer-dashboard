@extends('layouts.admin')



@section('head')
    <!-- Explicitly link Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')

<style>
    .btn{
        margin:5px;
    }
</style>
    <div class="container mt-5">
        <h1>Monitoring Results</h1>
        <a href="{{ route('monitoring.create') }}" class="btn btn-primary mb-3">Add Monitoring Data</a>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Monitor Time</th>
                    <th>Monitor Date</th>
                    <th>Score</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monitorings as $monitoring)
                    <tr>
                        <td>{{ $monitoring->id }}</td>
                        <td>{{ $monitoring->employee->name }}</td>
                        <td>{{ $monitoring->monitor_from }} - {{ $monitoring->monitor_to }}</td>
                        <td>{{ $monitoring->monitor_date }}</td>
                        <td>
                            @php
                                $scoreDetails = [
                                    'good' => ['icon' => 'check-circle-fill', 'class' => 'text-success'],
                                    'avg' => ['icon' => 'dash-circle-fill', 'class' => 'text-primary'],
                                    'bad' => ['icon' => 'x-circle-fill', 'class' => 'text-warning'],
                                    'worst' => ['icon' => 'exclamation-circle-fill', 'class' => 'text-danger']
                                ];
                                $score = strtolower($monitoring->score);
                                $details = $scoreDetails[$score] ?? ['icon' => 'question-circle-fill', 'class' => 'text-secondary'];
                            @endphp
                            <span class="{{ $details['class'] }}">
                                <i class="bi bi-{{ $details['icon'] }} me-2"></i>
                                {{ ucfirst($score) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('monitoring.show', $monitoring->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>


                              
                        

                                @can('update monitoring')
                                               
                                <a href="{{ route('monitoring.edit', $monitoring->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                             
                                
                                            
                                @endcan


                                @can('delete monitoring')
                                               
                               
                                               <form action="{{ route('monitoring.destroy', $monitoring->id) }}" method="POST" style="display:inline;">
                                                   @csrf
                                                   @method('DELETE')
                                                   <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                       <i class="bi bi-trash"></i> Delete
                                                   </button>
                                               </form>
               
                                                           
                                               @endcan

                           
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                No monitoring data available.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
    {{ $monitorings->links('pagination::bootstrap-4') }} <!-- Laravel pagination links with Bootstrap 4 -->
</div>

    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@endsection
