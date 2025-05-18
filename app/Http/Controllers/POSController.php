<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index()
    {
        return Inertia::render('pos/Index');
    }

    public function create()
    {
        return view('pos.create');
    }

    public function edit($id)
    {
        return view('pos.edit', compact('id'));
    }

    public function show($id)
    {
        return view('pos.show', compact('id'));
    }
    public function destroy($id)
    {
        // Logic to delete the POS entry
        return redirect()->route('pos.index')->with('success', 'POS entry deleted successfully.');
    }
}
