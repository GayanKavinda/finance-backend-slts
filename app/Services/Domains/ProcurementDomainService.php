<?php

namespace App\Services\Domains;

use App\Models\Tender;
use App\Models\ProjectJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * ProcurementDomainService
 * 
 * Manages the procurement lifecycle from Tender Opening to Awarding.
 */
class ProcurementDomainService
{
    /**
     * Create a new tender.
     */
    public function createTender(array $data)
    {
        return Tender::create($data);
    }

    /**
     * Award a tender and initialize the Project Job.
     * This is a cross-domain bridge (Procurement -> Project).
     */
    public function awardTender(Tender $tender, array $awardData)
    {
        return DB::transaction(function () use ($tender, $awardData) {
            $tender->update([
                'status' => 'Awarded',
                'awarded_amount' => $awardData['amount'],
                'notes' => $awardData['notes'] ?? $tender->notes,
            ]);

            // Auto-initialize the Project Job for execution tracking
            return ProjectJob::create([
                'tender_id' => $tender->id,
                'customer_id' => $tender->customer_id,
                'name' => "Project: " . $tender->name,
                'project_value' => $awardData['amount'],
                'description' => $tender->description,
                'status' => ProjectJob::STATUS_PENDING,
            ]);
        });
    }

    /**
     * Update tender status.
     */
    public function updateStatus(Tender $tender, string $status)
    {
        $tender->update(['status' => $status]);
        return $tender;
    }
}
