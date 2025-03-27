<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChemicalController extends Controller
{
    // Show add form
    public function create()
    {
        return view('chemicals.add');
    }

    // Store new chemical
    public function store(Request $request)
    {
        // Validate and store the chemical
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
        ]);

        // Example: Save to database (model should be created)
        // Chemical::create($request->all());

        return redirect('/dashboard')->with('success', 'Chemical added successfully!');
    }

    // Show remove form
    public function deleteForm()
    {
        return view('chemicals.remove');
    }

    // Remove chemical
    public function destroy(Request $request)
    {
        // Example: Delete from database
        // Chemical::where('id', $request->id)->delete();

        return redirect('/dashboard')->with('success', 'Chemical removed successfully!');
    }

    // Show edit form
    public function editForm()
    {
        return view('chemicals.edit');
    }

    // Update chemical
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:chemicals,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
        ]);

        // Example: Update database record
        // $chemical = Chemical::find($request->id);
        // $chemical->update($request->all());

        return redirect('/dashboard')->with('success', 'Chemical updated successfully!');
    }
}
