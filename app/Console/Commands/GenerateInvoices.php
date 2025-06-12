<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\FacturaDirectaClient;
use App\Models\Commission;

class GenerateInvoices extends Command
{
    protected $signature = 'invoices:generate';
    protected $description = 'Genera facturas para afiliados con comisiones aprobadas y reservas concluidas';

    public function handle()
    {
        $facturaClient = new FacturaDirectaClient(config('services.facturadirecta.token'));


        $commissions = Commission::where('status', 'approved')
            ->whereNull('invoice_id')
/*             ->whereHas('reservation', fn ($q) => $q->where('check_out_date', '<=', now())) */
            ->with('affiliate')
            ->get()
            ->groupBy('affiliate_id');

        if ($commissions->isEmpty()) {
            $this->info('No hay comisiones pendientes de facturar.');
            return;
        }

        foreach ($commissions as $affiliateId => $group) {
            $affiliate = $group->first()->affiliate;

            $client = $facturaClient->createClient([
                'name' => $affiliate->name,
                'email' => $affiliate->email,
                'language' => 'es',
            ]);

            $clientId = $client['id'] ?? null;
            if (!$clientId) {
                $this->error("No se pudo crear cliente para el afiliado {$affiliate->name}");
                continue;
            }

            $total = $group->sum('amount');

            $invoice = $facturaClient->createInvoice([
                'client_id' => $clientId,
                'lines' => [[
                    'description' => 'Comisiones por reservas concluidas',
                    'quantity' => 1,
                    'unit_price' => $total,
                ]],
                'notes' => 'Factura generada automáticamente por sistema de afiliados',
            ]);

            $invoiceId = $invoice['id'] ?? null;

            if ($invoiceId) {
                foreach ($group as $commission) {
                    $commission->update(['invoice_id' => $invoiceId]);
                }
                $this->info("Factura {$invoiceId} creada para afiliado {$affiliate->name}");
            } else {
                $this->error("Error al crear la factura para {$affiliate->name}");
            }
        }
    }
}
