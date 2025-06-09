<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Link;
use Inertia\Inertia;
class PropertyController extends Controller
{
    public function show(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $ref = $request->query('ref'); // Ej: 3-S5MllfEXUJ
        $affiliateLink = null;

        if ($ref) {
            $affiliateLink = Link::where('generated_url', 'like', "%ref=$ref")->first();

            // Contador de clics
            if ($affiliateLink) {
                $affiliateLink->increment('clicks');
            }
        }

        return Inertia::render('Property/Show', [
            'property' => $property,
            'affiliate_link_id' => $affiliateLink?->id,
        ]);
    }
}
