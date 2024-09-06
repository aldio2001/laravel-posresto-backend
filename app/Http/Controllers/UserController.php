<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Assuming you have a User model
use Illuminate\Support\Facades\Hash; // For password hashing

class UserController extends Controller
{
    // Index
    public function index (Request $request)
    {
        // Get all users with pagination
        $users = DB::table('users')
        ->when($request->input('name'), function($query, $name){
            $query->where('name','like','%'.$name .'%')
            ->orWhere('email','like','%'.$name .'%');
        })

        ->paginate(10);

        return view('pages.users.index', compact('users'));
    }

    // Create
    public function create ()
    {
        return view('pages.users.create');
    }

    // Store
    public function store(Request $request)
    {
        // Validate request...
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'roles' => 'required|in:admin,staff,user',
        ]);

        // Store the request...
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->roles = $request->roles;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // Show
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.show', compact('user'));
    }

    // Edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required|in:admin,staff,user',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->roles = $request->roles;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // Destroy
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
