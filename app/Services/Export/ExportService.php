<?php

namespace App\Services\Export;

use App\Models\MarketingPlan;
use PDF; // Barryvdh\DomPDF\Facade\Pdf
use Maatwebsite\Excel\Facades\Excel; // If we implement Excel detailed export class later

class ExportService
{
    public function exportPdf(MarketingPlan $plan)
    {
        // Load view with plan data
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('exports.plan-pdf', ['plan' => $plan->load('sections')]);
        return $pdf->download($plan->title . '.pdf');
    }

    public function exportExcel(MarketingPlan $plan)
    {
        // This would typically involve an Export class, but for simplicity we return a basic arrays structure
        // In full implementation, we'd use Maatwebsite Exportable classes
        return [
            'message' => 'Excel export logic placeholder'
        ];
    }
}
