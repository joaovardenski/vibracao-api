<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class TicketNumberService
{
    public function generate(): string
    {
        // Lock advisory global — só uma transação passa por vez neste ponto
        DB::statement('SELECT pg_advisory_xact_lock(12345)');

        $sequence = DB::table('orders')
            ->whereNotNull('ticket_number')
            ->count() + 1;

        return 'VJ' . str_pad(
            (string) $sequence,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}