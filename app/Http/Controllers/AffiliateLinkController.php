<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Property;
use App\Models\User;
use App\Models\Link;
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
    public function selectProperty()
    {
        $user = Auth::user();

        $properties = Property::all(); // o paginar/filtrar si hay muchas

        return Inertia::render('AffiliateLinks/SelectProperty', [
            'properties' => $properties,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $baseUrl = route('property.show', $request->property_id); // o como se construya
        $uniqueSuffix = substr(md5(uniqid()), 0, 6);

        $generatedUrl = $baseUrl . '?ref=' . $user->id . '-' . $uniqueSuffix;

        Link::create([
            'affiliate_id' => $user->id,
            'property_id' => $request->property_id,
            'name' => $request->name,
            'target_url' => $baseUrl,
            'generated_url' => $generatedUrl,
            'clicks' => 0,
            'conversions' => 0,
        ]);

        return redirect()->route('links')->with('success', 'Link generado correctamente.');
    }
    


}

