<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rows = [
            // id, description, date, qty (signed), price
            ['Pembelian', '2021-01-01', 40, 100],
            ['Penjualan', '2021-01-01', -20, 200],
            ['Penjualan', '2021-01-02', -10, 200],
            ['Pembelian', '2021-01-03', 20, 120],
            ['Pembelian', '2021-01-03', 10, 110],
            ['Penjualan', '2021-01-04', -5, 200],
            ['Penjualan', '2021-01-05', -8, 200],
            ['Pembelian', '2021-01-06', 15, 115],
            ['Penjualan', '2021-01-07', -20, 200],
            ['Penjualan', '2021-01-07', -15, 200],
            ['Pembelian', '2021-01-08', 10, 110],
            ['Penjualan', '2021-01-09', -5, 210],
            ['Penjualan', '2021-01-10', -6, 210],
            ['Pembelian', '2021-01-11', 4, 125],
            ['Penjualan', '2021-01-12', -5, 210],
        ];

        foreach ($rows as $r) {
            Transaction::create([
                'description' => $r[0],
                'date' => $r[1],
                'type' => $r[0],
                'qty' => $r[2],
                'price' => $r[3],
            ]);
        }

        // After inserting all, compute the computed columns
        app(\App\Http\Controllers\TransactionController::class)->recomputeAll();
    }
}
