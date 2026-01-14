<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Infografis;
use Illuminate\Http\Request;

class InfografisApiController extends Controller
{
    /**
     * List infografis for public frontend.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->get('limit', 0);

        $query = Infografis::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Show single infografis item.
     */
    public function show(Infografis $infografis)
    {
        return response()->json([
            'success' => true,
            'data' => $infografis,
        ]);
    }
}





