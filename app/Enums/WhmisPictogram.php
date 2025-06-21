<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * WHMIS hazard pictograms mapped to Heroicon names for Filament tables/forms.
 */
enum WhmisPictogram: string
{
    case FLAME = 'flame';
    case FLAME_OVER_CIRCLE = 'flame-over-circle';
    case GAS_CYLINDER = 'gas-cylinder';
    case SKULL_CROSSBONES = 'skull-crossbones';
    case CORROSION = 'corrosion';
    case HEALTH_HAZARD = 'health-hazard';
    case ENVIRONMENT = 'environment';
    case EXPLODING_BOMB = 'exploding-bomb';
    case BIOHAZARDOUS = 'biohazardous';
    case EXCLAMATION_MARK = 'exclamation-mark';

    /**
     * Returns the icon identifier used by Blade Icons / Filament.
     * This should correspond to the SVG filename placed in resources/svg.
     */
    public function icon(): string
    {
        return match ($this) {
            self::FLAME => 'flame',
            self::FLAME_OVER_CIRCLE => 'flame_circle',
            self::GAS_CYLINDER => 'gas_cylinder',
            self::SKULL_CROSSBONES => 'skull_crossbones',
            self::CORROSION => 'corrosion',
            self::HEALTH_HAZARD => 'health_hazard',
            self::ENVIRONMENT => 'environment',
            self::EXPLODING_BOMB => 'exploding_bomb',
            self::BIOHAZARDOUS => 'biohazardous',
            self::EXCLAMATION_MARK => 'exclamation_mark',
        };
    }
}
