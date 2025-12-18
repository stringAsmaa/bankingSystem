<?php

namespace App\Modules\administratives\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Models\Transaction;

class ReportService
{
    public function dailyTransactions(string $date): Collection
    {
        return Transaction::whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function accountSummaries(): Collection
    {
return BankAccount::withCount(['transactions as total_transactions'])
    ->withSum('transactions as total_deposits', 'transaction_amount')
    ->withSum('transactions as total_withdrawals', 'transaction_amount')
    ->get()
    ->map(function ($account) {
        return [
            'account_id' => $account->id,
            'user_id' => $account->user_id,
            'balance' => $account->balance,
            'total_deposits' => $account->total_deposits ?? 0,
            'total_withdrawals' => $account->total_withdrawals ?? 0,
            'transactions_count' => $account->total_transactions,
        ];
    });

    }

    public function auditLogs(string $from, string $to): Collection
    {
        return Activity::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();
    }

  public function exportCsv(Collection $data, string $filename): string
{
    $csvData = '';
    if ($data->isEmpty()) return '';

    // header
$firstRow = $data->first();
$firstRow = is_array($firstRow) ? $firstRow : $firstRow->toArray();

$csvData .= implode(',', array_keys($firstRow)) . "\n";

    foreach ($data as $row) {
        $rowArray = [];
foreach (is_array($row) ? $row : $row->toArray() as $value) {
    if (is_array($value) || is_object($value)) {
        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    $rowArray[] = '"'.$value.'"';
}

        $csvData .= implode(',', $rowArray) . "\n";
    }

$path = "reports/{$filename}.csv";
Storage::disk('public')->put($path, $csvData);

return Storage::url($path);

}

}
