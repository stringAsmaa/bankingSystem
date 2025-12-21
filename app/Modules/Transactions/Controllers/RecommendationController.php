<?php

namespace App\Modules\Transactions\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Services\RecommendationService;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function __construct(
        private RecommendationService $recommendationService
    ) {}

    public function index()
    {
        $transactions = Transaction::query()
            ->where('created_by_user_id', Auth::id())
            ->whereIn('transaction_status', [
                TransactionStatus::COMPLETED->value,
                TransactionStatus::APPROVED->value,
            ])
            ->get();

        if ($transactions->isEmpty()) {
            return ApiResponse::sendResponse(
                200,
                'No completed or approved transactions found for recommendations.',
                []
            );
        }

        $recommendations = $this->recommendationService->generate($transactions);

        if (empty($recommendations)) {
            return ApiResponse::sendResponse(
                200,
                'No personalized recommendations available at this time.',
                []
            );
        }

        return ApiResponse::sendResponse(
            200,
            'Personalized banking recommendations generated successfully.',
            $recommendations
        );
    }
}
