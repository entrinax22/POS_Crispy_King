<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Models\ReservedTable;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'table_id' => 'required|string',
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'reservation_time' => 'required|date',
            'number_guest' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
    
            $tableId = decrypt($request->input('table_id'));

            // Check table availability
            $table = Table::find($tableId);
            if (!$table || $table->status !== 'available') {
                return response()->json([
                    'result' => false,
                    'message' => 'Table is not available for reservation.',
                ], 404);
            }

            // Format reservation time
            $formattedTime = Carbon::parse($request->input('reservation_time'))->format('Y-m-d H:i:s');

            // Save reservation
            $reservation = ReservedTable::create([
                'table_id' => $tableId,
                'reserved_by' => auth()->user()->id,
                'name' => $request->input('name'),
                'contact_number' => $request->input('contact_number'),
                'number_guest' => $request->input('number_guest'),
                'reservation_time' => $formattedTime,
                'status' => 'pending',
            ]);

            // Update table status
            $table->status = 'reserved';
            $table->save();

            return response()->json([
                'result' => true,
                'message' => 'Table reserved successfully.',
                'data' => $reservation,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Reservation failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function listOfTables(){
        try{
            $tables = Table::select('table_id', 'table_number', 'status')
                ->where('status', 'available')
                ->orderBy('table_number')
                ->get()
                ->map(function ($table) {
                    return [
                        'table_id' => encrypt($table->table_id),
                        'table_number' => $table->table_number,
                    ];
                });

            return response()->json([
                'result' => true,
                'message' => 'Tables retrieved successfully.',
                'data' => $tables,
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error retrieving tables: ' . $e->getMessage(),
            ], 500);
        }
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
