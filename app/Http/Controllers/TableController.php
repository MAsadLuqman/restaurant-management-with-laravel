<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::with(['currentOrder', 'reservations' => function($query) {
            $query->today()->where('status', 'confirmed');
        }])->orderBy('table_number')->get();

        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables',
            'capacity' => 'required|integer|min:1|max:20',
        ]);

        $table = Table::create($request->all());
        $table->generateQRCode();

        return redirect()->route('tables.index')
            ->with('success', 'Table created successfully');
    }

    public function show(Table $table)
    {
        $table->load(['orders' => function($query) {
            $query->latest()->take(10);
        }, 'reservations' => function($query) {
            $query->upcoming()->orderBy('reservation_date');
        }]);

        return view('tables.show', compact('table'));
    }

    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update($request->all());

        return redirect()->route('tables.index')
            ->with('success', 'Table updated successfully');
    }

    public function destroy(Table $table)
    {
        if ($table->orders()->exists()) {
            return redirect()->route('tables.index')
                ->with('error', 'Cannot delete table with existing orders');
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Table deleted successfully');
    }

    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $table->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Table status updated successfully'
        ]);
    }

    public function generateQR(Table $table)
    {
        $qrCode = $table->generateQRCode();
        $qrCodeImage = QrCode::size(200)->generate(route('menu.public', ['table' => $table->qr_code]));

        return response()->json([
            'success' => true,
            'qr_code' => $qrCodeImage,
            'qr_url' => route('menu.public', ['table' => $table->qr_code])
        ]);
    }
}
