<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use Illuminate\Http\Request;
use App\Models\Pangan;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Pasar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->input('search_query');
    
        // Mengecek apakah hasil pencarian sudah ada di cache
        $cacheKey = 'barangs_' . md5($query);
        $barangs = Cache::remember($cacheKey, 60, function () use ($query) {
            if ($query) {
                return Barang::with('pangans')
                                ->where('nama', 'LIKE', "%$query%")
                                ->latest()
                                ->paginate(20);
            } else {
                return Barang::with('pangans')
                                ->latest()
                                ->paginate(20);
            }
        });
    
        return view('index', compact('barangs'));
    }
    
    // Fungsi untuk menangani pencarian barang
    public function search(Request $request)
    {
        $query = $request->input('search_query');
    
        // Mengecek apakah hasil pencarian sudah ada di cache
        $cacheKey = 'barangs_' . md5($query);
        $barangs = Cache::remember($cacheKey, 60, function () use ($query) {
            if ($query) {
                return Barang::with('pangans')
                                ->where('nama', 'LIKE', "%$query%")
                                ->latest()
                                ->paginate(20);
            } else {
                return Barang::with('pangans')
                                ->latest()
                                ->paginate(20);
            }
        });
    
        return view('index', compact('barangs'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $komoditas = Komoditas::find($id);

    //     return view('komoditas-show',compact($komoditas));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // public function allharga()
    // {
    //     $pangans = Pangan::latest()->get();
    //     return response()->json($pangans);
    // }

    public function komoditas()
    {

        $pasars = Pasar::select('nama')->latest()->get();
        // $komoditas = Komoditas::with('barangs.pangans')->latest()->get();

        // Ambil data komoditas dengan relasi barangs.pangans dan filter berdasarkan "Pasar Inpres Manonda"
        // $komoditas = Komoditas::with(['barangs.pangans' => function ($query) {
        //     $query->where('pasar', 'Pasar Inpres Manonda');
        // }])->latest()->get();


        // Tentukan pasar yang akan digunakan untuk filter
        $selectedPasar = request('filter');
        
        // Inisialisasi variabel untuk menyimpan hasil query
        $komoditas = null;

        // Jika filter tidak diberikan, atau jika filter kosong
        if ($selectedPasar) {
            // Ambil data komoditas dengan relasi barangs.pangans dan filter berdasarkan pasar yang ditentukan
            $komoditas = Komoditas::with(['barangs.pangans' => function ($query) use ($selectedPasar) {
                $query->where('pasar', $selectedPasar);
            }])->latest()->get();
        } else {
        // Ambil data komoditas dengan relasi barangs.pangans dan filter berdasarkan pasar yang ditentukan
        // $komoditas = Komoditas::with(['barangs.pangans' => function ($query) use ($selectedPasar) {
        //     $query->where('pasar', $selectedPasar);
        // }])->latest()->get();
        $komoditas = Komoditas::with(['barangs.pangans' => function ($query) {
            $query->where('pasar', 'Pasar Inpres Manonda');
        }])->latest()->get();
        
        }

        return view('komoditas',compact('komoditas','pasars'));
    }


    


}
