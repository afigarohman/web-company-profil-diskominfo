<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriApiController extends Controller
{
    /**
     * List galeri for public frontend.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->get('limit', 0);

        $query = Galeri::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

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
     * Show single galeri item.
     */
    public function show(Galeri $galeri)
    {
        return response()->json([
            'success' => true,
            'data' => $galeri,
        ]);
    }
}

