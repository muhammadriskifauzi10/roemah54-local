<?php

namespace App\Http\Middleware;

use App\Models\Kamar;
use App\Models\Lokasi;
use App\Models\Transaksi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasSewa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (
        //     Kamar::where('status', 0)
        //     ->when(Transaksi::count() > 0, function ($query) {
        //         return $query->leftjoin('transaksis', 'kamars.id', '=', 'transaksis.kamar_id')
        //             ->whereNull('transaksis.kamar_id')
        //             ->orWhere('transaksis.status_pembayaran', 'failed')
        //             ->orderBy('kamars.id', 'ASC')
        //             ->select('kamars.id', 'kamars.nomor_kamar');
        //     })
        //     ->get()->count() > 0
        // ) {
        //     return $next($request);
        // }

        // abort(404);
        if (Lokasi::where('jenisruangan_id', 2)->where('status', 0)->get()->count() > 0) {
            return $next($request);
        }

        abort(404);
    }
}
