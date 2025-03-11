<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::orderBy('name')->paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employé créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['schedules' => function($query) {
            $query->orderBy('date', 'desc');
        }])->findOrFail($id);
        
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employé mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employé supprimé avec succès.');
    }

    /**
     * Display the archive of employees.
     */
    public function archive()
    {
        $archivedEmployees = Employee::onlyTrashed()
            ->orderBy('name')
            ->paginate(10);
            
        return view('employees.archive', compact('archivedEmployees'));
    }

    /**
     * Restore the specified resource from archive.
     */
    public function restore(string $id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('employees.archive')
            ->with('success', 'Employé restauré avec succès.');
    }

    /**
     * Permanently delete the specified resource from archive.
     */
    public function forceDelete(string $id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        
        // Vérifier si l'employé a des plannings
        $schedulesCount = $employee->schedules()->withTrashed()->count();
        
        if ($schedulesCount > 0) {
            return redirect()->route('employees.archive')
                ->with('error', 'Impossible de supprimer définitivement cet employé car il a des plannings associés.');
        }
        
        $employee->forceDelete();

        return redirect()->route('employees.archive')
            ->with('success', 'Employé supprimé définitivement.');
    }
}
