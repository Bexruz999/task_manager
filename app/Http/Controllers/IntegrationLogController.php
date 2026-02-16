<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntegrationLogRequest;
use App\Http\Resources\IntegrationLogResource;
use App\Models\IntegrationLog;

class IntegrationLogController extends Controller
{
    public function index()
    {
        return IntegrationLogResource::collection(IntegrationLog::all());
    }

    public function store(IntegrationLogRequest $request)
    {
        return new IntegrationLogResource(IntegrationLog::create($request->validated()));
    }

    public function show(IntegrationLog $integrationLog)
    {
        return new IntegrationLogResource($integrationLog);
    }

    public function update(IntegrationLogRequest $request, IntegrationLog $integrationLog)
    {
        $integrationLog->update($request->validated());

        return new IntegrationLogResource($integrationLog);
    }

    public function destroy(IntegrationLog $integrationLog)
    {
        $integrationLog->delete();

        return response()->json();
    }
}
