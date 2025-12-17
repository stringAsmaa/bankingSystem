<?php

namespace App\Modules\administratives\Services;

use App\Models\User;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Enums\TransactionStatus;

class DashboardService
{
    public function getDashboardData($user): array
    {
        if ($user->hasRole('Admin')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('Manager')) {
            return $this->managerDashboard();
        }

        throw new \Exception('Unauthorized');
    }

    protected function adminDashboard(): array
    {
        return [
            'users_by_role' => [
                'admins'   => User::role('Admin')->count(),
                'managers' => User::role('Manager')->count(),
                'tellers'  => User::role('Teller')->count(),
            ],
            'users_count' => User::count(),
            'transactions_count' => Transaction::count(),
            'pending_transactions' => Transaction::where('transaction_status',TransactionStatus::PENDING)->count(),
            'rejected_transactions' => Transaction::where('transaction_status', TransactionStatus::REJECTED)->count(),
            'approved_transactions' => Transaction::where('transaction_status', TransactionStatus::APPROVED)->count(),
            'largest_transaction' => Transaction::max('transaction_amount'),
            'last_transaction' => Transaction::latest()->first()?->only(['id', 'transaction_amount', 'transaction_status']),
            'total_amount' => Transaction::sum('transaction_amount'),
            'approval_rate' =>  $this->approvalRate(),
        ];
    }

    protected function managerDashboard(): array
    {
        return [
            'transactions_count' => Transaction::count(),
            'pending_transactions' => Transaction::where('transaction_status',TransactionStatus::PENDING)->count(),
            'rejected_transactions' => Transaction::where('transaction_status', TransactionStatus::REJECTED)->count(),
            'approved_transactions' => Transaction::where('transaction_status', TransactionStatus::APPROVED)->count(),            'total_amount' => Transaction::sum('transaction_amount'),
            'today_transactions' => Transaction::whereDate('created_at', today())->count(),
            'pending_by_amount' => [
                'small'  => Transaction::where('transaction_status', 'pending')->where('transaction_amount', '<=', 5000)->count(),
                'medium' => Transaction::where('transaction_status', 'pending')->whereBetween('transaction_amount', [5001, 10000])->count(),
                'large'  => Transaction::where('transaction_status', 'pending')->where('transaction_amount', '>', 10000)->count(),
            ],
            'average_transaction_amount' => round(Transaction::avg('transaction_amount'), 2),

        ];
    }

    protected function approvalRate(): string
    {
        $total = Transaction::count();

        if ($total === 0) {
            return '0%';
        }

        return round(
            (Transaction::where('transaction_status', 'approved')->count() / $total) * 100,
            2
        ) . '%';
    }
}
