<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Komoditas;
use App\Models\Pasar;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;



test('Halaman Harga Pangan Dashboard bisa di render', function () {
    
    $response = $this->get('/dashboard/harga-pangan');

    $response->assertStatus(302);
});

test('Harga pangan dapat diinput', function () {
    $user = User::factory()->create([
      'password' => Hash::make('password'),
    ]);
  
    // Membuat entri komoditas dan barang untuk data pengujian
    $komoditas = Komoditas::factory()->create();
    $barang = Barang::factory()->create();
    $pasar = Pasar::factory()->create();
    $satuan = Satuan::factory()->create();
  
    // Data yang akan dikirim dalam permintaan POST (perbaiki dengan nilai valid)
    $data = [
      'komoditas_id' => $komoditas->id,
      'pasar' => $pasar->nama, // Gunakan ID pasar yang valid
      'satuan' => $satuan->nama, // Gunakan ID satuan yang valid
      'barang_id' => $barang->id,
      'harga' => 10000, // Harga yang sesuai dengan validasi
      'periode' => '2024-03-19',
    ];
  
    // Mengirimkan permintaan untuk menyimpan data
    $response = $this->actingAs($user)->post('/dashboard/harga-pangan', $data);
  
    // Memastikan respons redirect ke halaman yang benar (sesuaikan dengan routing Anda)
    $response->assertRedirect('/dashboard/harga-pangan');
  
    // Memastikan data berhasil tersimpan di database
    $this->assertDatabaseHas('pangans', [
      'komoditas_id' => $komoditas->id,
      'barang_id' => $barang->id,
      'pasar' => $pasar->nama,
      'satuan' => $satuan->nama,
      'harga' => 10000,
      'periode' => '2024-03-19',
    ]);
  });