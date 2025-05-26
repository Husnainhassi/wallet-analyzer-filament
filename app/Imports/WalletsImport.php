<?php

namespace App\Imports;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\ToModel;

class WalletsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $address = $row['trader'] ?? '';
        $roi = $this->cleanNumericValue($row['roi'] ?? 0);
        $winrate = $this->cleanNumericValue($row['winrate'] ?? 0);

        $wallet = Wallet::where('address', $address)->first();
        
        if ($roi > 20 && $winrate > 50) {
            if ($wallet) {
                $wallet->update([
                    'roi' => $roi,
                    'win_rate' => $winrate,
                    'updated_at' => now(),
                ]);
            } else {
                Wallet::create([
                    'address'   => $address,
                    'roi'       => $roi,
                    'win_rate'  => $winrate,
                ]);
            }
        }


        return true;
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
