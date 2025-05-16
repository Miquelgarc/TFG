<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AffiliateLinkController extends Controller
{
    public function redirect($code, Request $request)
    {
        // Buscar el link per codi
        $link = AffiliateLink::where('generated_url', $code)->first();

        if (!$link) {
            abort(404, 'Enllaç no trobat');
        }

        // Incrementar clics
        $link->increment('clicks');

        // (Opcional) Registrar clic detallat
        // Aquí pots afegir una entrada a una taula de logs
        // o registrar IP, referrer, etc.

        // Redirigir a la URL objectiu
        return redirect()->away($link->target_url);
    }
}

