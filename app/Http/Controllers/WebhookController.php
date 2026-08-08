<?php

namespace App\Http\Controllers;

use App\Services\Import\Beds24SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function beds24(Request $request, Beds24SyncService $service)
    {
        if ($request->header('X-Webhook-Token') !== config('services.beds24.webhook_token')) {
            abort(403);
        }

        $booking = $request->input('booking', $request->all());

        try {
            $service->upsertBooking($booking);
        } catch (\Throwable $e) {
            Log::warning('Beds24 webhook: no se pudo procesar el booking.', [
                'error' => $e->getMessage(),
                'booking' => $booking,
            ]);

            return response()->json(['ok' => false], 200);
        }

        return response()->json(['ok' => true]);
    }
}
