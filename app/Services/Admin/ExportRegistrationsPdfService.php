<?php

namespace App\Services\Admin;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportRegistrationsPdfService
{
    public function execute()
    {
        $participants = Order::query()
            ->with([
                'participant',
                'ticketLot',
            ])
            ->where('status', 'approved')
            ->join(
                'participants',
                'participants.id',
                '=',
                'orders.participant_id'
            )
            ->orderBy('participants.full_name')
            ->select('orders.*')
            ->get();

        $pdf = Pdf::loadView(
            'pdf.participants',
            [
                'participants' => $participants,
            ]
        );

        return $pdf->download(
            'participantes-vibracao-jovem-' .
            now()->format('Y-m-d-H-i') .
            '.pdf'
        );
    }
}