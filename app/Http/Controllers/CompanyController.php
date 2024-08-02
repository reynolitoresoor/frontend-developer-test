<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::all()->toArray();
        return array_reverse($company);
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
            'logo' => 'required',
            'name' => 'required',
            'status' => 'required'
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/uploads');
            $logoPath = str_replace('public/', '', $logoPath);
            } else {
            $logoPath = null;
        }

        $company = Company::create([
            'logo' => $logoPath,
            'name' => $validatedData['name'],
            'status' => $validatedData['status'],
            ]);
        return response()->json($company, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::find($id);
        return response()->json($company);
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
            'name' => 'required',
            'status' => 'required',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/uploads');
            $logoPath = str_replace('public/', '', $logoPath);
            } else {
            $logoPath = null;
        }
        
        if($request->hasFile('logo')) {
            $company = Company::where('id',$id)->update(
                ['logo'=> $logoPath, 
                 'name' => $request->name, 
                 'status' => $request->status]
            );
        } else {
            $company = Company::where('id',$id)->update(
                ['name' => $request->name, 
                 'status' => $request->status]
            );
        }
        return response()->json($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $company = Company::find($id);
        $company->delete();
        return response()->json('Company deleted!');
    }

}
