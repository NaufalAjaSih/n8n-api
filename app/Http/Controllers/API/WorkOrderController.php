<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WorkOrderController extends Controller
{
    public function store(Request $request)
    {
        if (!is_array($request->all())) {
            return response()->json([
                'message' => 'Payload must be an array of work orders'
            ], 422);
        }

        $saved = [];
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->all() as $index => $item) {
                $validator = Validator::make($item, [
                    'work_order_number' => 'required|string',
                    'asc_name' => 'required|string',
                    'consumer_name' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'index' => $index,
                        'errors' => $validator->errors()
                    ];
                    continue;
                }

                $purchaseDate = $this->normalizeDate($item['purchase_date'] ?? '');
                $lastMaintain = $this->normalizeDate($item['last_maintain_date'] ?? '');
                $installation = $this->normalizeDate($item['installation_date'] ?? '');

                DB::table('work_orders')->updateOrInsert(
                    [
                        'work_order_number' => $item['work_order_number'],
                    ],
                    [
                        'asc_name' => $item['asc_name'] ?? '',
                        'consumer_name' => $item['consumer_name'] ?? '',
                        'phone_number' => $item['phone_number'] ?? '',
                        'whatsapp_number' => $item['whatsapp_number'] ?? '',
                        'service_type' => $item['service_type'] ?? '',
                        'product_category' => $item['product_category'] ?? '',
                        'product_model' => $item['product_model'] ?? '',
                        'purchase_date' => $purchaseDate,
                        'last_maintain_date' => $lastMaintain,
                        'installation_date' => $installation,
                        'product_problem' => $item['product_problem'] ?? '',
                        'error_code' => $item['error_code'] ?? '',
                        'address' => $item['address'] ?? '',
                        'district_city_province' => $item['district_city_province'] ?? '',
                        'google_maps_url' => $item['google_maps_url'] ?? '',
                        'spk_date' => now()->toDateString(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $saved[] = $item['work_order_number'];
            }

            DB::commit();

            return response()->json([
                'message' => 'Work orders processed successfully',
                'saved_count' => count($saved),
                'saved_work_orders' => $saved,
                'errors' => $errors
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process work orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function normalizeDate(string $date): ?string
    {
        if ($date === '') {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
