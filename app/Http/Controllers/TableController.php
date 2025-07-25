<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(){
        return Inertia::render('tables/Index');
    }

    public function list(Request $request){
        $search = $request->input('search');
        $tables = Table::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('table_number', 'like', "%{$search}%");
            })
            ->orderByDesc('table_id')
            ->paginate(10);

        // Add encrypted_id property for security, keep original table_id
        $data = $tables->getCollection()->map(function ($table) {
            return [
                'table_id' => encrypt($table->table_id),
                'table_number' => $table->table_number,
                'status' => $table->status, 
            ];
        });

        return response()->json([
            'result' => true,
            'message' => 'Available tables retrieved successfully.',
            'data' => $data,
            'pagination' => [
                'total' => $tables->total(),
                'current_page' => $tables->currentPage(),
                'last_page' => $tables->lastPage(),
                'per_page' => $tables->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        if($request->has('table_id')){
            $id = decrypt($request->input('table_id'));
            $table = Table::findOrFail($id);
            $table->table_number = $request->input('table_number');
            $table->status = $request->input('status', 'available');
            $table->save();

            return response()->json([
                'result' => true,
                'message' => 'Table updated successfully.',
            ]);
        }else{
            // Validate the request data
            $request->validate([
                'table_number' => 'required|integer|unique:tables,table_number',
            ]);

            // Create a new table
            $table = new Table();
            $table->table_number = $request->input('table_number');
            $table->save();

            return response()->json([
                'result' => true,
                'message' => 'Table created successfully.',
            ]);
        }
    }

    public function edit($encryptedId)
    {
        $id = decrypt($encryptedId);
        $table = Table::findOrFail($id);

        return response()->json([
            'result' => true,
            'data' => [
                'table_id' => encrypt($table->table_id),
                'table_number' => $table->table_number,
                'status' => $table->status,
            ],
            'message' => 'Table retrieved successfully.',
        ]);
    }

    public function reserve(Request $request)
    {
        // Validate the request data
        $request->validate([
            'table_id' => 'required',
            'reserved_by' => 'required|integer',
            'name' => 'required|string|max:255',
            'reservation_time' => 'required|date',
        ]);
        
        $tableId = decrypt($request->input('table_id'));

        // Find the table
        $table = Table::findOrFail($tableId);

        // Create a new reservation
        $reservation = $table->reservedTables()->create([
            'reserved_by' => $request->input('reserved_by'),
            'name' => $request->input('name'),
            'reservation_time' => $request->input('reservation_time'),
            'status' => 'pending',
        ]);

        return response()->json([
            'results' => true,
            'message' => 'Table reserved successfully.',
            'data' => $reservation,
        ]);
    }

    public function destroy($encryptedId)
    {
        $id = decrypt($encryptedId);
        $table = Table::with('reservedTables')->findOrFail($id);

        $table->delete();

        return response()->json([
            'result' => true,
            'message' => 'Table deleted successfully.',
        ]);
    }
}
