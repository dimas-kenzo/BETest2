<?php

namespace App\Http\Controllers;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $data = Transaction::orderBy('date')->orderBy('id')->get();
        return response()->json($data);
    }

    // add data
    public function store(TransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            // convert input into to-be-saved fields
            $type = $request->type;
            $inputQty = (int)$request->qty;
            // store qty signed: Pembelian = +qty, Penjualan = -qty
            $qty = $type === 'Pembelian' ? $inputQty : -$inputQty;

            // create a preliminary record (without computed fields) to place it in sequence
            $t = Transaction::create([
                'description' => $request->description ?? $type,
                'date' => $request->date,
                'type' => $type,
                'qty' => $qty,
                'price' => $request->price,
            ]);

            // recompute entire ledger
            $ok = $this->recomputeAll();

            if (! $ok['success']) {
                // rollback and remove created record
                DB::rollBack();
                return response()->json(['error' => $ok['message']], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $t->fresh()], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // update data
    public function update(TransactionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            $type = $request->type;
            $inputQty = (int)$request->qty;
            $qty = $type === 'Pembelian' ? $inputQty : -$inputQty;

            $transaction->update([
                'description' => $request->description ?? $type,
                'date' => $request->date,
                'type' => $type,
                'qty' => $qty,
                'price' => $request->price,
            ]);

            $ok = $this->recomputeAll();

            if (! $ok['success']) {
                DB::rollBack();
                return response()->json(['error' => $ok['message']], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Data berhasil diupdate', 'data' => $transaction->fresh()]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // remove data
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);
            $transaction->delete();

            $ok = $this->recomputeAll();

            if (! $ok['success']) {
                DB::rollBack();
                return response()->json(['error' => $ok['message']], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Recompute full ledger ordered by date then id.
     * Returns ['success' => bool, 'message' => string]
     */
    public function recomputeAll()
    {
        // retrieve all transactions in sequence
        $txs = Transaction::orderBy('date')->orderBy('id')->get();

        $qtyBalance = 0;
        $valueBalance = 0.0;
        $hpp = 0.0;

        foreach ($txs as $tx) {
            // For each transaction, compute cost and total_cost, then update balances and hpp
            if ($tx->type === 'Pembelian') {
                // For purchases, cost = input price
                $cost = (float)$tx->price;
                $totalCost = $tx->qty * $cost; // qty positive
                $qtyBalance += $tx->qty;
                $valueBalance += $totalCost;

                // compute hpp if qtyBalance > 0
                if ($qtyBalance != 0) {
                    $hpp = $valueBalance / $qtyBalance;
                } else {
                    $hpp = 0.0;
                }
            } else { // Penjualan
                // For sales, unit cost used = current hpp (before applying this sale)
                $cost = $hpp;
                // qty for sale should be negative in table
                $totalCost = $tx->qty * $cost; // qty negative -> totalCost negative
                $qtyBalance += $tx->qty; // subtract
                $valueBalance += $totalCost; // subtract

                // hpp remains defined as valueBalance/qtyBalance when qtyBalance > 0
                if ($qtyBalance > 0) {
                    $hpp = $valueBalance / $qtyBalance;
                } elseif ($qtyBalance == 0) {
                    // when stock becomes 0, keep hpp as previous hpp (or set 0). We'll set hpp same as previous to avoid division by zero in later readings.
                    // but we store hpp as previous hpp
                    // keep $hpp as-is (already is previous)
                } else {
                    // negative stock -> invalid
                    return ['success' => false, 'message' => 'Stock menjadi minus pada transaksi id '. $tx->id .' (tanggal '. $tx->date->format('Y-m-d') .').'];
                }
            }

            // update tx computed fields
            $tx->cost = round($cost, 4);
            // make total_cost with 4 decimals
            $tx->total_cost = round($totalCost, 4);
            $tx->qty_balance = $qtyBalance;
            $tx->value_balance = round($valueBalance, 4);
            // store hpp with higher precision
            $tx->hpp = $qtyBalance > 0 ? round($hpp, 8) : round($hpp, 8);

            // save
            $tx->save();
        }

        // all ok
        return ['success' => true, 'message' => 'OK'];
    }
}
