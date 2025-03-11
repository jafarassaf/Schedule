@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Rapport Mensuel</h1>
            <div class="btn-group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <a href="{{ route('reports.annual') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt"></i> Rapport annuel
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
                <form method="GET" action="{{ route('reports.monthly') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="month" class="form-label">Mois</label>
                        <select name="month" id="month" class="form-select">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="year" class="form-label">Année</label>
                        <select name="year" id="year" class="form-select">
                            @foreach(range(date('Y')-2, date('Y')+1) as $y)
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

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Heures travaillées - {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</h5>
                <button class="btn btn-sm btn-outline-light" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
            <div class="card-body">
                @if($schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Poste</th>
                                    <th>Jours travaillés</th>
                                    <th>Total heures</th>
                                    <th>Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalHours = 0;
                                @endphp

                                @foreach($schedules as $employeeId => $employeeSchedules)
                                    @php
                                        $employee = $employeeSchedules->first()->employee;
                                        $employeeHours = $employeeSchedules->sum('hours_worked');
                                        $totalHours += $employeeHours;
                                        $daysWorked = $employeeSchedules->pluck('date')->unique()->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('employees.show', $employeeId) }}">
                                                {{ $employee->name }}
                                            </a>
                                        </td>
                                        <td>{{ $employee->position ?? '-' }}</td>
                                        <td>{{ $daysWorked }}</td>
                                        <td class="fw-bold">{{ number_format($employeeHours, 2) }} h</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $employeeId }}">
                                                <i class="fas fa-info-circle"></i> Voir détails
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="details-{{ $employeeId }}">
                                        <td colspan="5">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr class="table-secondary">
                                                            <th>Date</th>
                                                            <th>Début</th>
                                                            <th>Fin</th>
                                                            <th>Heures</th>
                                                            <th>Statut</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($employeeSchedules->sortBy('date') as $schedule)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                                                <td>{{ $schedule->hours_worked }} h</td>
                                                                <td>
                                                                    @if($schedule->is_completed)
                                                                        <span class="badge bg-success">Terminé</span>
                                                                    @else
                                                                        <span class="badge bg-warning">En cours</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end fw-bold">Total général :</td>
                                    <td class="fw-bold">{{ number_format($totalHours, 2) }} h</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun planning trouvé pour cette période.
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
        // Ajouter ici des scripts spécifiques au rapport mensuel si nécessaire
    });
</script>
@endsection 