<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentInterface;
use App\Enums\OrderStatus;
use App\Jobs\SendSmsJob;
use App\Models\Order;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(protected PaymentInterface $paymentService) {}

    public function handlePayment(Request $request)
    {
        $signature = $request->header('X-Signature');
        $payload = $request->all();

        if (!$signature || !$this->paymentService->verifyWebhook($payload, $signature)) {

            return response()->json([
                'message' => 'Невалидная подпись',
                'errors' => ['signature' => ['HMAC-SHA256 не прошёл проверку']]
            ], 403);
        }

        $order = Order::find($payload['order_id']);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($payload['status'] === 'success') {
            $currentStatus = OrderStatus::from($order->status);

            if ($currentStatus->canTransitionTo(OrderStatus::Paid)) {
                $order->update(['status' => OrderStatus::Paid->value, 'paid_at' => now(),]);

                SendSmsJob::dispatch($order->customer_phone, "Оплата прошла успешно! Заказ $order->id принят.");

                return response()->json(['message' => 'Order status updated to paid']);
            }
        }

        return response()->json(['message' => 'Webhook received but no action taken']);
    }
}
