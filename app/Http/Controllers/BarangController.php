<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Komoditas;

use Illuminate\Http\Request;
use Alert;
class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $barangs = Barang::latest()->paginate(10);
        $komoditas = Komoditas::latest()->get();
        if (request('search')) {
            $barangs = Barang::where('nama', 'like', '%' . request('search') . '%')->latest()->paginate(10);
        } 
        
        return view('dashboard.barang.index',compact('barangs','komoditas'));
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
        $validatedData = $request->validate([
            'nama' => 'required|string|max:50',
            'komoditas_id' => 'required|exists:komoditas,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
    
        // Memastikan file gambar ada sebelum mencoba menyimpannya
        if($request->hasFile('image')){
            // Menyimpan gambar ke direktori yang ditentukan
            $imagePath = $request->file('image')->store('barang-image', 'public');
            // Menggunakan path gambar yang disimpan untuk menyimpan dalam database
            $validatedData['image'] = $imagePath;
        }
    
        // Membuat record Barang dengan data yang divalidasi
        Barang::create($validatedData);
        $request->session(Alert::success('success', 'Barang berhasil ditambahkan!'));
        return redirect('/dashboard/barang');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);
        $barang->nama = $request->input('nama');

        $request->validate([
            'nama' => 'required',       
        ]);
       
        $barang->update($request->all());
        

            $request->session(Alert::success('success', 'Barang berhasil diupdate!'));
            return redirect('/dashboard/barang');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,$id)
    {
       
        $barang = Barang::find($id);

        
        $barang->delete();

        $request->session(Alert::success('success', 'Barang berhasil dihapus!'));
            return redirect('/dashboard/barang');
    }
}
