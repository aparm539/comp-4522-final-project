<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * Custom Filament field that provides an inline QR scanner and writes the scanned
 * value into the form state.
 */
final class QrScannerField extends Field
{
    protected string $view = 'forms.components.qr-scanner-field';

    public static function make(string $name): static
    {
        /** @var static $field */
        $field = parent::make($name);
        return $field;
    }
} 