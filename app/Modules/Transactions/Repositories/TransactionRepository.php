<?php

namespace App\Modules\Transactions\Repositories;

use App\Modules\Transactions\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findByReference(string $reference): ?Transaction
    {
        return Transaction::where('transaction_reference', $reference)->first();
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function save(Transaction $transaction): Transaction
    {
        $transaction->save();

        return $transaction;
    }
}
