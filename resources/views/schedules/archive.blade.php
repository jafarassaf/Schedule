@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Historique des plannings</h1>
            <div class="btn-group">
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux plannings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Filtres</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('schedules.archive') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="employee_id" class="form-label">Employé</label>
                        <select name="employee_id" id="employee_id" class="form-select">
                            <option value="">Tous les employés</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('schedules.archive') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-eraser"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Plannings archivés</h5>
            </div>
            <div class="card-body">
                @if($archivedSchedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Date</th>
                                    <th>Horaires</th>
                                    <th>Heures</th>
                                    <th>Supprimé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archivedSchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->employee->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </td>
                                        <td>{{ $schedule->hours_worked }} h</td>
                                        <td>{{ $schedule->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <form action="{{ route('schedules.restore', $schedule->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('schedules.force_delete', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce planning ? Cette action est irréversible.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer définitivement">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $archivedSchedules->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun planning archivé ne correspond à vos critères de recherche.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 