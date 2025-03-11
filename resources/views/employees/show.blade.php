@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Détails de l'employé</h1>
            <div class="btn-group">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Informations personnelles</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">Nom</h6>
                    <p>{{ $employee->name }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Email</h6>
                    <p>{{ $employee->email ?? 'Non renseigné' }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Téléphone</h6>
                    <p>{{ $employee->phone ?? 'Non renseigné' }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Poste</h6>
                    <p>{{ $employee->position ?? 'Non renseigné' }}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Statut</h6>
                    @if($employee->is_active)
                        <span class="badge bg-success">Actif</span>
                    @else
                        <span class="badge bg-danger">Inactif</span>
                    @endif
                </div>
                
                @if($employee->notes)
                <div class="mb-3">
                    <h6 class="fw-bold">Notes</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $employee->notes }}
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
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
    
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Plannings récents</h5>
            </div>
            <div class="card-body">
                @if($employee->schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Horaires</th>
                                    <th>Heures</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->schedules->sortByDesc('date')->take(10) as $schedule)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </td>
                                        <td>{{ $schedule->hours_worked }} h</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('schedules.create', ['employee_id' => $employee->id, 'date' => date('Y-m-d')]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Ajouter un planning
                        </a>
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun planning trouvé pour cet employé.
                        <a href="{{ route('schedules.create', ['employee_id' => $employee->id, 'date' => date('Y-m-d')]) }}" class="alert-link">
                            Ajouter un planning
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 