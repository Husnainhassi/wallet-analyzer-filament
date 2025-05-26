<?php

namespace App\Filament\Imports;

use App\Models\Wallet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class WalletImporter extends Importer
{
    protected static ?string $model = Wallet::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('address')
                ->label('Wallet Address') 
                ->rules(['required'])
                ->requiredMapping(),
            ImportColumn::make('roi')
                ->label('ROI') 
                ->numeric()
                ->rules(['numeric']),
            ImportColumn::make('winrate')
                ->label('Winrate') 
                ->numeric()
                ->rules(['numeric']),
        ];
    }

    public function resolveRecord(): ?Wallet
    {
        $roi = $this->cleanNumericValue($this->data['roi'] ?? 0);
        $winrate = $this->cleanNumericValue($this->data['winrate'] ?? 0);
        $wallet = $this->data['address'] ?? '';

        if ($roi <= 20 || $winrate <= 50) {
            return null;
        }

        return Wallet::firstOrNew(['address' => $wallet])->fill([
            'roi' => $roi,
            'winrate' => $winrate,
            'updated_at' => now(),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Imported ' . number_format($import->successful_rows) . ' valid wallets (ROI > 20, Winrate > 50).';

        if ($import->getFailedRowsCount()) {
            $body .= ' ' . number_format($import->getFailedRowsCount()) . ' rows skipped (invalid data).';
        }

        return $body;
    }

    private function cleanNumericValue($value)
    {
        if (is_string($value)) {
            $cleaned = str_replace('%', '', $value);
            return (float)$cleaned;
        }
        
        return (float)$value;
    }
}
