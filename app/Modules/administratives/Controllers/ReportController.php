<?php

namespace App\Modules\administratives\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Administratives\Services\ReportService;
use App\Modules\administratives\Requests\AuditLogsRequest;
use App\Modules\administratives\Requests\AccountSummariesRequest;
use App\Modules\administratives\Requests\DailyTransactionsRequest;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function dailyTransactions(DailyTransactionsRequest $request)
    {
        $data = $this->service->dailyTransactions($request->validatedData()['date']);

        $path = $this->service->exportCsv($data,  'daily_transactions_' . $request->validatedData()['date']);
        return response()->json(['download_path' => $path]);
        return response()->json($data);
    }

    public function accountSummaries(AccountSummariesRequest $request)
    {
        $data = $this->service->accountSummaries();

        $path = $this->service->exportCsv($data, "account_summaries_" . now()->toDateString());
        return response()->json(['download_path' => $path]);


        return response()->json($data);
    }

    public function auditLogs(AuditLogsRequest $request)
    {
        $from = $request->from;
        $to   = $request->to ?? now()->toDateString();
        $data = $this->service->auditLogs($from, $to);
        $path = $this->service->exportCsv($data, "audit_logs_{$from}_to_{$to}");
        return response()->json(['download_path' => $path]);


        return response()->json($data);
    }
}
