<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Services\FacturaDirectaClient;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show($invoiceId)
{
    $user = Auth::user();

    // Verifica que la factura pertenece al afiliado autenticado
    $commission = Commission::where('invoice_id', $invoiceId)
        ->where('affiliate_id', $user->id)
        ->firstOrFail();

    $pdfUrl = "https://app.factuura.com/invoices/{$invoiceId}/download"; // Ajusta si usas otro dominio o método

    return redirect()->away($pdfUrl);
}

}
