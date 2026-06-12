<?php

namespace App\Domain\Registration\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ExportRegistrationsPdfService
{
    public function execute(): Response
    {
        $participants = Order::query()
            ->select('orders.*')
            ->join('participants', 'participants.id', '=', 'orders.participant_id')
            ->with(['participant', 'ticketLot'])
            ->where('orders.status', 'approved')
            ->orderBy('participants.full_name')
            ->get();

        $pdf = Pdf::loadView('pdf.participants', [
            'participants' => $participants,
        ]);

        $filename = sprintf('participantes-vibracao-jovem-%s.pdf', now()->format('Y-m-d-H-i'));

        return $pdf->download($filename);
    }
}