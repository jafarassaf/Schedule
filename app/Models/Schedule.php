<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use SoftDeletes;

    /**
     * Les attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'store_id',
        'date',
        'start_time',
        'end_time',
        'hours_worked',
        'is_completed',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours_worked' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

    /**
     * Obtenir l'employé associé à ce planning.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Obtenir le magasin associé à ce planning.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Calculer les heures travaillées avant la sauvegarde.
     */
    protected static function booted()
    {
        static::saving(function ($schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = new \DateTime($schedule->start_time);
                $end = new \DateTime($schedule->end_time);
                
                // Si l'heure de fin est inférieure à l'heure de début, cela signifie que le travail
                // s'étend sur deux jours (ex: 22h00 à 6h00)
                if ($start > $end) {
                    // On ajoute 1 jour à l'heure de fin pour calculer correctement
                    $end->modify('+1 day');
                }
                
                $interval = $start->diff($end);
                $hours = $interval->h + ($interval->i / 60);
                
                // Si le travail s'étend sur plus d'un jour, ajouter les heures des jours supplémentaires
                if ($interval->days > 0) {
                    $hours += $interval->days * 24;
                }
                
                $schedule->hours_worked = round($hours, 2);
            }
        });
    }
}
