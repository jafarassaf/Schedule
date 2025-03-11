@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Gestion des employés</h1>
            <div class="btn-group">
                <a href="{{ route('employees.archive') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-history"></i> Employés archivés
                </a>
                <a href="{{ route('employees.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Ajouter un employé
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Liste des employés</h5>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('employees.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher un employé..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($employees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Poste</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>
                                            <a href="{{ route('employees.show', $employee->id) }}">
                                                {{ $employee->name }}
                                            </a>
                                        </td>
                                        <td>{{ $employee->email ?? '-' }}</td>
                                        <td>{{ $employee->phone ?? '-' }}</td>
                                        <td>{{ $employee->position ?? '-' }}</td>
                                        <td>
                                            @if($employee->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
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
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $employees->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun employé trouvé.
                        <a href="{{ route('employees.create') }}" class="alert-link">Ajouter un employé</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 