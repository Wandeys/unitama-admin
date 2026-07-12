<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    'password' => 'required|string|min:8', // standard secure length
    'passwordconfirm' => 'required|same:password', // standard secure length
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1048', // assuming an image upload
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
    
    'avatar.image' => 'Avatar harus berupa gambar.',
    'avatar.mimes' => 'Format gambar avatar harus jpeg, png, atau jpg.',
    'avatar.max' => 'Ukuran avatar tidak boleh lebih dari 1MB.',
    
    'role.required' => 'Role wajib dipilih.',
    'role.in' => 'Role harus berupa Superadmin atau Admin.',
]);

    try{

if($request->file('avatar')){
    $validated['avatar'] = $request->file('avatar')->store('avatar', 'public');
}

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
    public function show(string $id)
    {
        //
    }

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
}
