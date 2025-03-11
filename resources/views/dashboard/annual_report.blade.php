@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Rapport Annuel</h1>
            <div class="btn-group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <a href="{{ route('reports.monthly') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-week"></i> Rapport mensuel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Sélectionner une période</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.annual') }}" class="row g-3 align-items-end">
                    <div class="col-md-10">
                        <label for="year" class="form-label">Année</label>
                        <select name="year" id="year" class="form-select">
                            @foreach(range(date('Y')-3, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Résumé annuel {{ $year }}</h5>
                <button class="btn btn-sm btn-outline-light" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
            <div class="card-body">
                @if(count($monthlyHours) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="table-primary">
                                            <th>Mois</th>
                                            <th>Total heures</th>
                                            <th>Nombre d'employés</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAnnualHours = 0;
                                            $monthNames = [
                                                '01' => 'Janvier',
                                                '02' => 'Février',
                                                '03' => 'Mars',
                                                '04' => 'Avril',
                                                '05' => 'Mai',
                                                '06' => 'Juin',
                                                '07' => 'Juillet',
                                                '08' => 'Août',
                                                '09' => 'Septembre',
                                                '10' => 'Octobre',
                                                '11' => 'Novembre',
                                                '12' => 'Décembre',
                                            ];
                                        @endphp
                                        
                                        @foreach($monthNames as $monthNum => $monthName)
                                            @php
                                                $monthHours = isset($monthlyHours[$monthNum]) 
                                                    ? $monthlyHours[$monthNum]->sum('hours_worked') 
                                                    : 0;
                                                $totalAnnualHours += $monthHours;
                                                
                                                $employeeCount = isset($monthlyHours[$monthNum]) 
                                                    ? $monthlyHours[$monthNum]->pluck('employee_id')->unique()->count() 
                                                    : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reports.monthly', ['month' => $monthNum, 'year' => $year]) }}">
                                                        {{ $monthName }}
                                                    </a>
                                                </td>
                                                <td class="text-end">{{ number_format($monthHours, 2) }} h</td>
                                                <td class="text-center">{{ $employeeCount }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <td class="fw-bold">Total</td>
                                            <td class="text-end fw-bold">{{ number_format($totalAnnualHours, 2) }} h</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Répartition par employé</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $employeeData = [];
                                        foreach ($monthlyHours as $month => $schedules) {
                                            foreach ($schedules as $schedule) {
                                                $employeeId = $schedule->employee_id;
                                                if (!isset($employeeData[$employeeId])) {
                                                    $employeeData[$employeeId] = [
                                                        'name' => $schedule->employee->name,
                                                        'hours' => 0,
                                                        'days' => []
                                                    ];
                                                }
                                                $employeeData[$employeeId]['hours'] += $schedule->hours_worked;
                                                $employeeData[$employeeId]['days'][] = $schedule->date;
                                            }
                                        }
                                        
                                        // Trier par heures décroissantes
                                        uasort($employeeData, function($a, $b) {
                                            return $b['hours'] <=> $a['hours'];
                                        });
                                    @endphp
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="table-primary">
                                                    <th>Employé</th>
                                                    <th>Heures travaillées</th>
                                                    <th>Jours travaillés</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($employeeData as $employeeId => $data)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('employees.show', $employeeId) }}">
                                                                {{ $data['name'] }}
                                                            </a>
                                                        </td>
                                                        <td class="text-end">{{ number_format($data['hours'], 2) }} h</td>
                                                        <td class="text-center">{{ count(array_unique($data['days'])) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucune donnée disponible pour l'année {{ $year }}.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter ici des scripts spécifiques au rapport annuel si nécessaire
    });
</script>
@endsection 