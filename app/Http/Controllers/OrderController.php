<?php

namespace App\Http\Controllers;

use App\Contracts\GeocoderInterface;
use App\Contracts\RoutingInterface;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\SendSmsJob;
use App\Models\IntegrationLog;
use App\Models\Order;
use DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct(
        protected GeocoderInterface $geocoder,
        protected RoutingInterface  $routing
    )
    {
    }

    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function store(Request $request)
    {
        // 1. Validatsiya
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_email' => 'required|email',
            'origin_address' => 'required|string',
            'destination_address' => 'required|string',
        ]);

        $startTime = microtime(true);

        return DB::transaction(function () use ($validated, $startTime) {
            // 2. Geokodlash (Origin va Destination uchun)
            $originPoint = $this->geocoder->geocode($validated['origin_address']);
            $destPoint = $this->geocoder->geocode($validated['destination_address']);

            // 3. Marshrut hisoblash
            $route = $this->routing->calculateRoute($originPoint, $destPoint);

            // 4. Narxni hisoblash (Base: 5000, +800 per km, >100km bo'lsa *1.5)
            $distance = $route['distance'];
            $cost = 5000 + ($distance * 800);
            if ($distance > 100) $cost *= 1.5;

            // 5. Orderni saqlash
            $order = Order::create([
                ...$validated,
                'origin_lat' => $originPoint['lat'],
                'origin_lng' => $originPoint['lng'],
                'destination_lat' => $destPoint['lat'],
                'destination_lng' => $destPoint['lng'],
                'distance_km' => $distance,
                'duration_minutes' => $route['duration'],
                'estimated_cost' => $cost,
                'status' => 'pending',
            ]);

            // 6. Loglash (Talab: integration_logs jadvaliga)
            $this->logIntegration('geocoder & routing', 'POST', null, $validated, $route, 200, $startTime);

            SendSmsJob::dispatch($order->customer_phone, "Ваш заказ создан. ID: #$order->id");

            return response()->json([
                'data' => $order,
                'message' => 'Заказ создан, SMS отправлено'
            ], 201);
        });
    }

    private function logIntegration($service, $method, $url, $req, $res, $status, $start)
    {
        IntegrationLog::create([
            'service' => $service,
            'method' => $method,
            'url' => $url,
            'request_body' => $req,
            'response_body' => $res,
            'status_code' => $status,
            'duration_ms' => round((microtime(true) - $start) * 1000),
        ]);
    }


    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    public function update(OrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json();
    }
}
