@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Modifier le planning</h1>
            <div class="btn-group">
                <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux détails
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Formulaire de modification</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('schedules.update', $schedule->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employé</label>
                        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un employé</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $schedule->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="store_id" class="form-label">Restaurant</label>
                        <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un restaurant</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ $schedule->store_id == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('store_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ $schedule->date }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Heure de début</label>
                                <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Heure de fin</label>
                                <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_completed" id="is_completed" class="form-check-input" {{ $schedule->is_completed ? 'checked' : '' }} value="1">
                        <label for="is_completed" class="form-check-label">Marquer comme terminé</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ $schedule->notes }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Pour les horaires qui s'étendent sur deux jours, nous permettons maintenant
    // que l'heure de fin soit avant l'heure de début (ex: début à 22h, fin à 6h)
    document.getElementById('end_time').addEventListener('change', function() {
        const startTime = document.getElementById('start_time').value;
        const endTime = this.value;
        
        // Message informatif pour l'utilisateur
        if (startTime && endTime && startTime > endTime) {
            const confirmation = confirm("L'heure de fin est avant l'heure de début. Cela signifie que le planning s'étendra sur deux jours. Est-ce correct ?");
            if (!confirmation) {
                this.value = '{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}';
            }
        }
    });
</script>
@endsection 