@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Diagramme Horaire</h1>
            <div class="btn-group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Sélectionner une date et un restaurant</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.timeline', $selectedStore ? $selectedStore->id : '') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="store_id" class="form-label">Restaurant</label>
                        <div class="d-flex flex-wrap">
                            @foreach($stores as $store)
                                <a href="{{ route('reports.timeline', ['store_id' => $store->id, 'date' => $date->format('Y-m-d')]) }}" 
                                   class="btn {{ $selectedStore && $selectedStore->id === $store->id ? 'btn-primary' : 'btn-outline-primary' }} me-2 mb-2">
                                    {{ $store->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Afficher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    Emploi du temps {{ $selectedStore ? 'de ' . $selectedStore->name : '' }} ({{ $date->format('d/m/Y') }})
                </h5>
            </div>
            <div class="card-body">
                @if($schedulesByEmployee->count() > 0)
                    <div class="timeline-container my-4">
                        <div style="width: 100%; overflow-x: auto;">
                            <div style="min-width: 1200px;">
                                <!-- En-tête avec les heures -->
                                <div class="d-flex border-bottom mb-3">
                                    <div style="width: 150px; font-weight: bold; padding: 10px;">Employé</div>
                                    <div style="flex-grow: 1; display: flex;">
                                        @for($hour = 0; $hour <= 24; $hour++)
                                            <div style="flex-grow: 1; text-align: center; padding: 5px; border-left: 1px dashed #ddd;">
                                                {{ $hour }}
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                
                                <!-- Lignes des employés avec leurs horaires -->
                                @foreach($schedulesByEmployee as $employeeId => $employeeSchedules)
                                    @php
                                        $employee = $employeeSchedules->first()->employee;
                                        $employeeColor = App\Helpers\ColorHelper::getEmployeeColor($employeeId);
                                    @endphp
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 150px; font-weight: bold; padding: 10px;">
                                            {{ $employee->name }}
                                        </div>
                                        <div style="flex-grow: 1; position: relative; height: 30px; border-left: 1px solid #aaa; border-right: 1px solid #aaa;">
                                            <!-- Grille d'arrière-plan -->
                                            <div style="display: flex; position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
                                                @for($hour = 0; $hour < 25; $hour++)
                                                    <div style="flex-grow: 1; border-left: 1px dashed #ddd;"></div>
                                                @endfor
                                            </div>
                                            
                                            <!-- Barres de plannings -->
                                            @foreach($employeeSchedules as $schedule)
                                                @php
                                                    $startHour = (float) \Carbon\Carbon::parse($schedule->start_time)->format('H');
                                                    $startMinutes = (float) \Carbon\Carbon::parse($schedule->start_time)->format('i') / 60;
                                                    $startTime = $startHour + $startMinutes;
                                                    
                                                    $endHour = (float) \Carbon\Carbon::parse($schedule->end_time)->format('H');
                                                    $endMinutes = (float) \Carbon\Carbon::parse($schedule->end_time)->format('i') / 60;
                                                    $endTime = $endHour + $endMinutes;
                                                    
                                                    // Si l'heure de fin est avant l'heure de début, c'est un shift qui passe minuit
                                                    if ($endTime < $startTime) {
                                                        $endTime += 24;
                                                    }
                                                    
                                                    $left = ($startTime / 24) * 100;
                                                    $width = (($endTime - $startTime) / 24) * 100;
                                                @endphp
                                                
                                                <div style="position: absolute; top: 0; left: {{ $left }}%; width: {{ $width }}%; height: 30px; background-color: {{ $employeeColor }}; border-radius: 4px;"
                                                     title="{{ $employee->name }}: {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun planning trouvé pour ce restaurant à cette date.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 