<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.index',[
            'title' => 'Dashboard',
        ]);
    }

    

   

    /**
     * Display the specified resource.
     */
    public function show()
    {
         return view('dashboard.show',[
            'title' => 'Detail User',
            'user' =>  Auth::user(),
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('dashboard.edit',[
            'title' => 'Edit User',
            'user' =>  Auth::user(),
        ]); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    'password' => 'nullable|string|min:8', 
    'passwordconfirm' => 'nullable|same:password', 
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Diubah ke 1024 KB agar pas 1MB
    
], [
    'name.required' => 'Nama tidak boleh kosong.',
    'name.max' => 'Nama tidak boleh lebih dari :max karakter.',
    
    'email.required' => 'Email tidak boleh kosong.',
    'email.email' => 'Format email tidak valid.',
    'email.unique' => 'Email ini sudah terdaftar.',
    'email.max' => 'Email tidak boleh lebih dari :max karakter.',
    
    'password.required' => 'Password tidak boleh kosong.',
    'password.min' => 'Password minimal harus terdiri dari :min karakter.',
    
    // Pesan error baru untuk konfirmasi password
    'passwordconfirm.required' => 'Konfirmasi password wajib diisi.',
    'passwordconfirm.same' => 'Konfirmasi password tidak cocok dengan password utama.',
    
    'avatar.image' => 'Avatar harus berupa gambar.',
    'avatar.mimes' => 'Format gambar avatar harus jpeg, png, atau jpg.',
    'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 1MB.',
    
  
]);

DB::beginTransaction();
   try{

if($request->file('avatar')){
    $validated['avatar'] = $request->file('avatar')->store('avatar', 'public');
    if($user->avatar){
        Storage::disk('public')->delete($user->avatar);
    }
}

if($request->password){
    $validated['password'] = bcrypt($request->password);
} else {
    unset($validated['password']);
}

       
         $user->update($validated);
     DB::commit();
     return to_route('dashboard.show')->withSuccess('Data berhasil diubah');
    } catch(\Exception $e){
    DB::rollBack();
     return to_route('dashboard.edit')->withError('Data gagal diubah');
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
