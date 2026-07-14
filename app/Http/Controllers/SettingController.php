<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('setting.index',[
            'title' => 'Setting',
            'setting'=> Setting::first(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
        'app_name' => 'required|string|max:255',
        'login_title' => 'required|string|max:255',
        'copyright' => 'required|string|max:255',
        'keywords' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Diubah ke 1024 KB agar pas 1MB
    
], [
    'app_name.required' => 'Nama aplikasi tidak boleh kosong.',
    'app_name.max' => 'Nama aplikasi tidak boleh lebih dari :max karakter.',
    
    'login_title.required' => 'Judul halaman login tidak boleh kosong.',
    'login_title.max' => 'Judul halaman login tidak boleh lebih dari :max karakter.',
    
    'copyright.required' => 'Hak cipta tidak boleh kosong.',
    'copyright.max' => 'Hak cipta tidak boleh lebih dari :max karakter.',
    
    'keywords.string' => 'Kata kunci harus berupa string.',
    'keywords.max' => 'Kata kunci tidak boleh lebih dari :max karakter.',
    
    'description.string' => 'Deskripsi harus berupa string.',
    
    'logo.image' => 'Berkas yang diunggahharus berupa gambar.',
    'logo.mimes' => 'Format gambar  harus jpeg, png, atau jpg.',
    'logo.max' => 'Ukuran gambar tidak boleh lebih dari 1MB.',
    
    // Pesan error baru untuk konfirmasi password
    'passwordconfirm.required' => 'Konfirmasi password wajib diisi.',
    'passwordconfirm.same' => 'Konfirmasi password tidak cocok dengan password utama.',
    
    'avatar.image' => 'Avatar harus berupa gambar.',
    'avatar.mimes' => 'Format gambar avatar harus jpeg, png, atau jpg.',
    'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 1MB.',
    
    'role.required' => 'Role wajib dipilih.',
    'role.in' => 'Role harus berupa Superadmin atau Admin.',
]);

DB::beginTransaction();
   try{

if($request->file('logo')){
    $validated['logo'] = $request->file('logo')->store('logo', 'public');
    if($setting->logo){
        Storage::disk('public')->delete($setting->logo);
    }
}

       
         $setting->update($validated);
     DB::commit();
     return to_route('setting.index')->withSuccess('Data berhasil disimpan');
    } catch(\Exception $e){
    DB::rollBack();
     return to_route('setting.index')->withError('Data gagal disimpan');
    }
    }

}
