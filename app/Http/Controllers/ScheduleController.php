<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::today();
        
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        
        // Si aucun store_id n'est spécifié et qu'il y a des stores, on prend le premier
        $storeId = $request->input('store_id');
        if (!$storeId && $stores->count() > 0) {
            $storeId = $stores->first()->id;
        }
        
        $query = Schedule::with('employee', 'store')
            ->whereDate('date', $date);
        
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $schedules = $query->orderBy('start_time')->get();
            
        return view('schedules.index', [
            'schedules' => $schedules,
            'date' => $date,
            'stores' => $stores,
            'currentStoreId' => $storeId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employees = Employee::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::today();
            
        $defaultStoreId = $request->input('store_id');
        $defaultEmployeeId = $request->input('employee_id');
            
        return view('schedules.create', [
            'employees' => $employees,
            'stores' => $stores,
            'date' => $date,
            'defaultStoreId' => $defaultStoreId,
            'defaultEmployeeId' => $defaultEmployeeId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'store_id' => 'required|exists:stores,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Gestion des horaires qui s'étendent sur deux jours
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        // Si l'heure de fin est supérieure à l'heure de début, on garde les heures telles quelles
        // Si l'heure de fin est inférieure à l'heure de début, cela signifie que l'horaire s'étend sur deux jours
        // Dans ce cas, nous avons simplement besoin de sauvegarder les heures telles quelles puisque la logique de calcul
        // des heures travaillées dans le modèle Schedule en tiendra compte

        Schedule::create($validated);

        return redirect()
            ->route('schedules.index', ['date' => $request->input('date')])
            ->with('success', 'Planning créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with('employee')->findOrFail($id);
        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        $employees = Employee::where('is_active', true)
            ->orderBy('name')
            ->get();
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('schedules.edit', [
            'schedule' => $schedule,
            'employees' => $employees,
            'stores' => $stores,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'store_id' => 'required|exists:stores,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_completed' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Gestion des horaires qui s'étendent sur deux jours (mêmes règles que dans la méthode store)

        $schedule = Schedule::findOrFail($id);
        $schedule->update($validated);

        return redirect()
            ->route('schedules.index', ['date' => $request->input('date')])
            ->with('success', 'Planning mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        $date = $schedule->date;
        $schedule->delete();

        return redirect()
            ->route('schedules.index', ['date' => $date])
            ->with('success', 'Planning supprimé avec succès.');
    }

    /**
     * Display the archive of schedules.
     */
    public function archive(Request $request)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::today();
        
        $archivedSchedules = Schedule::onlyTrashed()
            ->with('employee')
            ->when($request->filled('date'), function($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($request->filled('employee_id'), function($query) use ($request) {
                $query->where('employee_id', $request->input('employee_id'));
            })
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        $employees = Employee::orderBy('name')->get();
            
        return view('schedules.archive', [
            'archivedSchedules' => $archivedSchedules,
            'date' => $date,
            'employees' => $employees,
        ]);
    }

    /**
     * Restore the specified resource from archive.
     */
    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);
        $schedule->restore();

        return redirect()
            ->route('schedules.archive')
            ->with('success', 'Planning restauré avec succès.');
    }

    /**
     * Permanently delete the specified resource from archive.
     */
    public function forceDelete($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);
        $schedule->forceDelete();

        return redirect()
            ->route('schedules.archive')
            ->with('success', 'Planning définitivement supprimé.');
    }
}
