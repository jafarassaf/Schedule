@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Tableau de bord</h1>
            <div class="btn-group">
                <a href="{{ route('schedules.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter un planning
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">Total employés</h5>
                <p class="display-4">{{ $employeeCount }}</p>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">Employés actifs</h5>
                <p class="display-4">{{ $activeEmployees }}</p>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title">Plannings du jour</h5>
                <p class="display-4">{{ $todaySchedules->count() }}</p>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Calendrier - {{ $date->format('F Y') }}</h5>
                <div class="btn-group">
                    <a href="{{ route('dashboard', ['date' => $date->copy()->subMonth()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i> Mois précédent
                    </a>
                    <a href="{{ route('dashboard', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                        Aujourd'hui
                    </a>
                    <a href="{{ route('dashboard', ['date' => $date->copy()->addMonth()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                        Mois suivant <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Légende des employés -->
                <div class="mb-3 border p-2 rounded bg-light">
                    <div class="d-flex align-items-center mb-1">
                        <strong class="me-2">Employés :</strong>
                        @php
                            $employees = App\Models\Employee::where('is_active', true)->orderBy('name')->get();
                        @endphp
                        <div class="d-flex flex-wrap">
                            @foreach($employees as $employee)
                                <div class="me-2 mb-1">
                                    <span class="badge rounded-pill px-3 py-2" style="{{ App\Helpers\ColorHelper::getEmployeeColorStyle($employee->id) }}">
                                        {{ $employee->name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Légende du calendrier -->
                <div class="d-flex justify-content-end mb-3">
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-primary text-white rounded px-2 py-1 me-1" style="width: 25px; height: 25px;"></div>
                        <span>Aujourd'hui</span>
                    </div>
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-warning text-dark rounded px-2 py-1 me-1" style="width: 25px; height: 25px;"></div>
                        <span>Weekend</span>
                    </div>
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-light text-muted rounded px-2 py-1 me-1" style="width: 25px; height: 25px;"></div>
                        <span>Autre mois</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded px-2 py-1 me-1" style="width: 25px; height: 25px;"></div>
                        <span>Plannings</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center bg-light">
                                <th style="width: 14.28%;">Lun</th>
                                <th style="width: 14.28%;">Mar</th>
                                <th style="width: 14.28%;">Mer</th>
                                <th style="width: 14.28%;">Jeu</th>
                                <th style="width: 14.28%;">Ven</th>
                                <th style="width: 14.28%;">Sam</th>
                                <th style="width: 14.28%;">Dim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $startOfMonth = $date->copy()->startOfMonth();
                                $endOfMonth = $date->copy()->endOfMonth();
                                
                                // Obtenir le premier jour du mois et ajuster au début de la semaine
                                $startDay = $startOfMonth->copy()->startOfWeek(1); // 1 = lundi
                                
                                // Obtenir le dernier jour du mois et ajuster à la fin de la semaine
                                $endDay = $endOfMonth->copy()->endOfWeek(0); // 0 = dimanche
                                
                                $currentDay = $startDay->copy();
                                
                                // Récupérer tous les stores
                                $stores = App\Models\Store::where('is_active', true)->get();
                            @endphp
                            
                            @while ($currentDay <= $endDay)
                                @if ($currentDay->dayOfWeek === 1)
                                    <tr>
                                @endif
                                
                                <td class="text-center {{ $currentDay->month !== $date->month ? 'text-muted bg-light' : '' }} {{ $currentDay->isToday() ? 'bg-primary text-white' : '' }}" style="height: 100px; position: relative; padding: 5px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge {{ $currentDay->isWeekend() ? 'bg-warning' : 'bg-secondary' }} fs-6">
                                            {{ $currentDay->day }}
                                        </span>
                                        
                                        <a href="{{ route('schedules.index', ['date' => $currentDay->format('Y-m-d')]) }}" class="btn btn-sm {{ $currentDay->month !== $date->month ? 'btn-outline-secondary' : 'btn-outline-primary' }}" title="Voir les plannings du jour">
                                            <i class="fas fa-calendar-day"></i>
                                        </a>
                                    </div>
                                    
                                    @foreach($stores as $store)
                                        @php
                                            // Récupérer tous les plannings pour ce jour et ce magasin
                                            $daySchedules = App\Models\Schedule::whereDate('date', $currentDay->format('Y-m-d'))
                                                ->where('store_id', $store->id)
                                                ->with('employee')
                                                ->get();
                                        @endphp
                                        
                                        @if ($daySchedules->count() > 0)
                                            <div class="mt-1">
                                                <span class="badge bg-info text-dark" style="font-size: 0.7rem;">
                                                    {{ Str::limit($store->name, 10) }}
                                                </span>
                                                <div class="mt-1">
                                                    @foreach($daySchedules as $schedule)
                                                        <a href="{{ route('schedules.show', $schedule->id) }}" class="d-block mb-1" style="text-decoration:none;">
                                                            <div class="progress" style="height: 15px; cursor: pointer;" title="{{ $schedule->employee->name }}: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                                                                <div class="progress-bar" style="width: 100%; {{ App\Helpers\ColorHelper::getEmployeeColorStyle($schedule->employee_id) }}">
                                                                    <span style="font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                
                                @if ($currentDay->dayOfWeek === 0)
                                    </tr>
                                @endif
                                
                                @php
                                    $currentDay->addDay();
                                @endphp
                            @endwhile
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Plannings du jour - {{ $date->format('d/m/Y') }}</h5>
                
                <div class="btn-group">
                    <a href="{{ route('schedules.index', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list"></i> Tous les plannings
                    </a>
                    <a href="{{ route('schedules.create', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Ajouter
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if ($todaySchedules->count() > 0)
                    <!-- Regrouper les plannings par restaurant -->
                    @php
                        $schedulesByStore = $todaySchedules->groupBy('store_id');
                    @endphp
                    
                    @foreach($schedulesByStore as $storeId => $storeSchedules)
                        @php
                            $storeName = 'Non assigné';
                            if ($storeId) {
                                $store = App\Models\Store::find($storeId);
                                if ($store) {
                                    $storeName = $store->name;
                                }
                            }
                        @endphp
                        
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-info">{{ $storeName }}</span>
                                <span class="badge bg-secondary">{{ $storeSchedules->count() }} {{ $storeSchedules->count() > 1 ? 'plannings' : 'planning' }}</span>
                            </h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Employé</th>
                                            <th style="width: 50%;">Horaires</th>
                                            <th style="width: 10%;">Heures</th>
                                            <th style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($storeSchedules->sortBy('start_time') as $schedule)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('employees.show', $schedule->employee_id) }}" class="badge rounded-pill mb-0 px-3 py-2" style="{{ App\Helpers\ColorHelper::getEmployeeColorStyle($schedule->employee_id) }}">
                                                        {{ $schedule->employee->name }}
                                                    </a>
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
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        Aucun planning n'est prévu pour aujourd'hui.
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

@section('scripts')
<script>
    // Si besoin d'ajouter du JavaScript spécifique à cette page
</script>
@endsection 