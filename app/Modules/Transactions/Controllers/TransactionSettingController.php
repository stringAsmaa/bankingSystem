<?php

namespace App\Modules\Transactions\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Modules\Transactions\Models\TransactionSetting;
use App\Modules\Transactions\Requests\UpdateTransactionSettingRequest;

class TransactionSettingController extends Controller
{

    public function update(UpdateTransactionSettingRequest $request)
    {
        $setting = TransactionSetting::firstOrCreate([]);

        $data = $request->only([
            'min_amount',
            'max_amount',
            'currency',
        ]);

        if (isset($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        $setting->update($data);

        return ApiResponse::sendResponse(
            200,
            'Transaction settings updated successfully.',
            $setting
        );
    }
}
