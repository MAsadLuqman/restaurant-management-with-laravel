<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $reservations = Reservation::with('table')
            ->orderBy('reservation_date', 'desc')
            ->paginate(20);

        $todayReservations = Reservation::with('table')
            ->today()
            ->orderBy('reservation_date')
            ->get();

        return view('reservations.index', compact('reservations', 'todayReservations'));
    }

    public function create()
    {
        $tables = Table::where('status', 'available')->orderBy('table_number')->get();
        return view('reservations.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date|after:now',
            'party_size' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string',
        ]);

        // Check if table is available at the requested time
        $existingReservation = Reservation::where('table_id', $request->table_id)
            ->where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingReservation) {
            return back()->withErrors(['table_id' => 'Table is not available at the selected time']);
        }

        $reservation = Reservation::create($request->all());

        // Send WhatsApp confirmation
        if ($request->customer_phone) {
            $this->sendReservationConfirmation($reservation);
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('table');
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $tables = Table::where('status', 'available')
            ->orWhere('id', $reservation->table_id)
            ->orderBy('table_number')
            ->get();

        return view('reservations.edit', compact('reservation', 'tables'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date',
            'party_size' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled',
        ]);

        $reservation->update($request->all());

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,seated,completed,cancelled'
        ]);

        $reservation->update(['status' => $request->status]);

        // Update table status based on reservation status
        if ($request->status === 'seated') {
            $reservation->table->update(['status' => 'occupied']);
        } elseif (in_array($request->status, ['completed', 'cancelled'])) {
            $reservation->table->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservation status updated successfully'
        ]);
    }

    public function publicStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'reservation_date' => 'required|date|after:now',
            'party_size' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string',
        ]);

        // Find available table for the party size
        $availableTable = Table::where('capacity', '>=', $request->party_size)
            ->where('status', 'available')
            ->orderBy('capacity')
            ->first();

        if (!$availableTable) {
            return response()->json([
                'success' => false,
                'message' => 'No tables available for the requested party size'
            ], 400);
        }

        $reservation = Reservation::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'table_id' => $availableTable->id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'special_requests' => $request->special_requests,
            'status' => 'pending'
        ]);

        // Send confirmation
        if ($request->customer_phone) {
            $this->sendReservationConfirmation($reservation);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservation request submitted successfully',
            'reservation' => $reservation
        ]);
    }

    private function sendReservationConfirmation(Reservation $reservation)
    {
        $message = "🍽️ *Reservation Confirmation*\n\n" .
            "Name: {$reservation->customer_name}\n" .
            "Date: " . $reservation->reservation_date->format('M d, Y h:i A') . "\n" .
            "Table: {$reservation->table->table_number}\n" .
            "Party Size: {$reservation->party_size}\n" .
            "Status: " . ucfirst($reservation->status) . "\n\n" .
            "We look forward to serving you! 🙏";

        $this->whatsAppService->sendMessage($reservation->customer_phone, $message);
    }
}
