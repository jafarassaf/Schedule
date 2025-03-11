@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Détails du planning</h1>
            <div class="btn-group">
                <a href="{{ route('schedules.index', ['date' => $schedule->date]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux plannings
                </a>
                <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Planning du {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Employé</h6>
                        <p class="mb-1">
                            <a href="{{ route('employees.show', $schedule->employee_id) }}">
                                {{ $schedule->employee->name }}
                            </a>
                        </p>
                        @if($schedule->employee->position)
                            <p class="small text-muted">{{ $schedule->employee->position }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Restaurant</h6>
                        <p>
                            @if($schedule->store)
                                <span class="badge bg-info">{{ $schedule->store->name }}</span>
                            @else
                                <span class="badge bg-secondary">Non assigné</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Statut</h6>
                        <p>
                            @if($schedule->is_completed)
                                <span class="badge bg-success">Terminé</span>
                            @else
                                <span class="badge bg-warning">En cours</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Heure de début</h6>
                        <p>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Heure de fin</h6>
                        <p>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Heures travaillées</h6>
                        <p>{{ $schedule->hours_worked }} heures</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Créé le</h6>
                        <p>{{ $schedule->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($schedule->notes)
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold">Notes</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $schedule->notes }}
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce planning ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 