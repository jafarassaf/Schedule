@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Plannings du {{ $date->format('d/m/Y') }}</h1>
            <div class="btn-group">
                <a href="{{ route('schedules.archive') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-history"></i> Historique
                </a>
                <a href="{{ route('schedules.create', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter un planning
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Naviguer entre les jours</h5>
                    <div class="btn-group">
                        <a href="{{ route('schedules.index', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-chevron-left"></i> Jour précédent
                        </a>
                        <a href="{{ route('schedules.index', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                            Aujourd'hui
                        </a>
                        <a href="{{ route('schedules.index', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                            Jour suivant <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <form method="GET" action="{{ route('schedules.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Date</span>
                                <input type="date" name="date" id="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Restaurant</span>
                                <select name="store_id" id="store_id" class="form-select" required>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ $currentStoreId == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-eraser"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Liste des plannings</h5>
            </div>
            <div class="card-body">
                @if ($schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Employé</th>
                                    <th style="width: 15%;">Restaurant</th>
                                    <th style="width: 40%;">Horaires</th>
                                    <th style="width: 10%;">Heures</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <a href="{{ route('employees.show', $schedule->employee_id) }}" class="badge rounded-pill mb-0 px-3 py-2" style="{{ App\Helpers\ColorHelper::getEmployeeColorStyle($schedule->employee_id) }}">
                                                {{ $schedule->employee->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($schedule->store)
                                                <span class="badge bg-info">{{ $schedule->store->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Non assigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="text-end pe-2" style="width: 60px;">
                                                    <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                                </div>
                                                <div class="flex-grow-1 px-2">
                                                    <div class="progress" style="height: 20px;">
                                                        @php
                                                            $startHour = (int)\Carbon\Carbon::parse($schedule->start_time)->format('H');
                                                            $endHour = (int)\Carbon\Carbon::parse($schedule->end_time)->format('H');
                                                            
                                                            // Si l'heure de fin est avant l'heure de début, c'est un shift qui passe minuit
                                                            if ($endHour < $startHour) {
                                                                $endHour += 24; // Ajouter 24h pour calculer correctement
                                                            }
                                                            
                                                            // Calculer la position de début (en pourcentage de 24h)
                                                            $startPercent = ($startHour / 24) * 100;
                                                            
                                                            // Calculer la largeur (durée en pourcentage de 24h)
                                                            $width = (($endHour - $startHour) / 24) * 100;
                                                            
                                                            // Limiter à 100% maximum
                                                            $width = min($width, 100 - $startPercent);
                                                            
                                                            // Récupérer la couleur de l'employé
                                                            $employeeColor = App\Helpers\ColorHelper::getEmployeeColor($schedule->employee_id);
                                                            
                                                            // Déterminer si le planning est terminé
                                                            $barColor = $schedule->is_completed ? 'success' : 'primary';
                                                            
                                                            // Si le shift traverse minuit, ajouter une seconde barre
                                                            $hasSecondBar = ($endHour > 24);
                                                            $secondWidth = 0;
                                                            
                                                            if ($hasSecondBar) {
                                                                $secondWidth = ((($endHour - 24) / 24) * 100);
                                                            }
                                                        @endphp
                                                        
                                                        <div class="progress-bar" 
                                                             role="progressbar" 
                                                             style="width: {{ $width }}%; margin-left: {{ $startPercent }}%; background-color: {{ $employeeColor }};" 
                                                             aria-valuenow="{{ $width }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100"
                                                             data-bs-toggle="tooltip"
                                                             title="{{ $schedule->employee->name }}: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                                                        </div>
                                                        
                                                        @if($hasSecondBar)
                                                            <div class="progress-bar" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $secondWidth }}%; background-color: {{ $employeeColor }};" 
                                                                 aria-valuenow="{{ $secondWidth }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-start ps-2" style="width: 60px;">
                                                    <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $schedule->hours_worked }} h</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce planning ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun planning n'est prévu pour cette journée.
                        <a href="{{ route('schedules.create', ['date' => $date->format('Y-m-d')]) }}" class="alert-link">
                            Ajouter un planning
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection 