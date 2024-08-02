<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where('type','Writer')->get()->toArray();
        return array_reverse($user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required'
        ]);


        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'type' => 'Writer',
            'status' => 'Active'
            ]);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json($user);
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
        $validatedData = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required',
            'password' => 'required_with:confirm_password|same:confirm_password'
        ]);
        
        if(!empty($request->passwoed) && !empty($request->confirm_password) && $request->password === $request->confirm_password) {
            $user = User::where('id',$id)->update(
                ['firstname'=> $request->firstname, 
                 'lastname' => $request->lastname, 
                 'email' => $request->email,
                 'password' => Hash::make($request->password)]
            );
        } else {
            $user = User::where('id',$id)->update(
                ['firstname'=> $request->firstname, 
                 'lastname' => $request->lastname, 
                 'email' => $request->email]
            );
        }
        return response()->json($user);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('User deleted!');
    }

}
