<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return view('user.index',[
            'title' => 'User',
            'users'=> User::latest()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('user.create',[
            'title' => 'Tambah User',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
 $validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users,email', 
    'password' => 'required|string|min:8', 
    'passwordconfirm' => 'required|same:password', 
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Diubah ke 1024 KB agar pas 1MB
    'role' => 'required|in:Superadmin,Admin',
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
    
    'role.required' => 'Role wajib dipilih.',
    'role.in' => 'Role harus berupa Superadmin atau Admin.',
]);

DB::beginTransaction();
    try{

if($request->file('avatar')){
    $validated['avatar'] = $request->file('avatar')->store('avatar', 'public');
}

 $validated['password'] = bcrypt($request->password);
 $validated['email_verified_at'] = now();

        DB::beginTransaction();
         $user = User::create($validated);
     DB::commit();
     return to_route('user.index')->withSuccess('Data berhasil ditambahkan');
    } catch(\Exception $e){
    DB::rollBack();
     return to_route('user.create')->withError('Data gagal ditambahkan');
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('user.show',[
            'title' => 'Detail User',
            'user' => $user,
        ]);   
         }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.edit',[
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
         $validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    'password' => 'nullable|string|min:8', 
    'passwordconfirm' => 'nullable|same:password', 
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // Diubah ke 1024 KB agar pas 1MB
    'role' => 'required|in:Superadmin,Admin',
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
    
    'role.required' => 'Role wajib dipilih.',
    'role.in' => 'Role harus berupa Superadmin atau Admin.',
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
     return to_route('user.index')->withSuccess('Data berhasil diubah');
    } catch(\Exception $e){
    DB::rollBack();
     return to_route('user.edit', $user)->withError('Data gagal diubah');
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try{
        $user->delete();
         if($user->avatar){
        Storage::disk('public')->delete($user->avatar);
    }

    DB::commit();
     return to_route('user.index')->withSuccess('Data berhasil dihapus');
    }catch(\Exception $e){
    DB::rollBack();
     return to_route('user.index')->withError('Data gagal dihapus');
    }
        }
    }

