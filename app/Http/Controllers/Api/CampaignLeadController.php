<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CampaignLeadRequest;
use App\Models\CampaignLead;
use Illuminate\Http\JsonResponse;

class CampaignLeadController extends Controller
{
    /**
     * Store a new campaign lead.
     */
    public function store(CampaignLeadRequest $request): JsonResponse
    {
        $lead = CampaignLead::create($request->validated());

        return response()->json([
            'id' => $lead->id,
            'message' => 'Your callback request has been received. We will contact you soon.',
        ], 201);
    }
}
