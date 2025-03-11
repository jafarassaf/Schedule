<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal.
     */
    public function index()
    {
        $today = Carbon::today();
        $todaySchedules = Schedule::with('employee')
            ->whereDate('date', $today)
            ->orderBy('start_time')
            ->get();
        
        $employeeCount = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        
        return view('dashboard.index', [
            'todaySchedules' => $todaySchedules,
            'date' => $today,
            'employeeCount' => $employeeCount,
            'activeEmployees' => $activeEmployees,
        ]);
    }

    /**
     * Affiche le rapport mensuel.
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $schedules = Schedule::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('employee_id');
        
        return view('dashboard.monthly_report', [
            'schedules' => $schedules,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Affiche le rapport annuel.
     */
    public function annualReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        
        $schedules = Schedule::with('employee')
            ->whereYear('date', $year)
            ->get();
        
        $monthlyHours = $schedules->groupBy(function($schedule) {
            return Carbon::parse($schedule->date)->format('m');
        });
        
        return view('dashboard.annual_report', [
            'monthlyHours' => $monthlyHours,
            'year' => $year,
        ]);
    }

    /**
     * Affiche le diagramme horaire par restaurant.
     */
    public function timelineReport(Request $request, $storeId = null)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date')) 
            : Carbon::today();
        
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        
        // Si aucun store_id n'est spécifié et qu'il y a des stores, on prend le premier
        if (!$storeId && $stores->count() > 0) {
            $storeId = $stores->first()->id;
        }
        
        // Récupérer tous les plannings pour ce jour et ce restaurant
        $schedules = Schedule::where('store_id', $storeId)
            ->whereDate('date', $date)
            ->with('employee')
            ->get();
        
        // Regrouper les plannings par employé
        $schedulesByEmployee = $schedules->groupBy('employee_id');
        
        // Récupérer le store sélectionné
        $selectedStore = Store::find($storeId);
        
        return view('dashboard.timeline', [
            'date' => $date,
            'stores' => $stores,
            'selectedStore' => $selectedStore,
            'schedulesByEmployee' => $schedulesByEmployee
        ]);
    }
}
