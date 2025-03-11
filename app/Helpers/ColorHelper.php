<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Une liste de couleurs distinctes pour représenter les différents employés
     * Ces couleurs sont suffisamment contrastées pour être facilement identifiables
     */
    private static $colors = [
        '#4285F4', // Bleu Google
        '#EA4335', // Rouge
        '#FBBC05', // Jaune
        '#34A853', // Vert
        '#8E44AD', // Violet
        '#F39C12', // Orange
        '#1ABC9C', // Turquoise
        '#E74C3C', // Rouge foncé
        '#2ECC71', // Vert clair
        '#3498DB', // Bleu clair
        '#D35400', // Orange foncé
        '#16A085', // Vert-bleu
        '#C0392B', // Rouge brique
        '#27AE60', // Émeraude
        '#9B59B6', // Violet clair
        '#2980B9', // Bleu foncé
    ];

    /**
     * Récupère une couleur pour un employé spécifique basée sur son ID
     * Garantit que le même employé aura toujours la même couleur
     *
     * @param int $employeeId ID de l'employé
     * @return string Code couleur hexadécimal
     */
    public static function getEmployeeColor($employeeId)
    {
        // Utiliser le modulo pour s'assurer que l'index est dans les limites du tableau
        $colorIndex = ($employeeId - 1) % count(self::$colors);
        return self::$colors[$colorIndex];
    }

    /**
     * Génère un style CSS pour l'arrière-plan et la couleur de texte appropriée
     *
     * @param int $employeeId ID de l'employé
     * @return string Style CSS
     */
    public static function getEmployeeColorStyle($employeeId)
    {
        $bgColor = self::getEmployeeColor($employeeId);
        
        // Détermine si le texte doit être blanc ou noir en fonction de la luminosité de la couleur
        $textColor = self::isLightColor($bgColor) ? '#000000' : '#FFFFFF';
        
        return "background-color: {$bgColor}; color: {$textColor};";
    }

    /**
     * Détermine si une couleur est claire ou foncée
     * Utilisé pour déterminer si le texte doit être noir (sur fond clair) ou blanc (sur fond foncé)
     *
     * @param string $hexColor Code couleur hexadécimal
     * @return bool True si la couleur est claire
     */
    private static function isLightColor($hexColor)
    {
        // Convertir la couleur hexadécimale en RGB
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Formule de luminosité perçue (basée sur la formule YIQ)
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Si la luminosité est > 128, c'est une couleur claire
        return $brightness > 128;
    }
} 