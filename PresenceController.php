<?php

namespace App\Http\Controllers\Api;

use App\Models\Presence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    /**
     * Fungsi untuk mencatat presensi (store).
     */
    public function attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'status' => 'required|in:hadir,tidak hadir,izin,sakit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah presensi untuk tanggal ini sudah ada
        $existingPresence = Presence::where('user_id', $request->user_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingPresence) {
            return response()->json([
                'status' => false,
                'message' => 'Presensi untuk tanggal ini sudah ada.',
            ], 400);
        }

        // Simpan presensi baru
        $presence = Presence::create($request->only(['user_id', 'date', 'time', 'status']));

        return response()->json([
            'status' => true,
            'message' => 'Presensi berhasil dicatat.',
            'data' => $presence,
        ]);
    }

    /**
     * Fungsi untuk melihat riwayat presensi.
     */
    public function history($user_id)
    {
        $presences = Presence::where('user_id', $user_id)->get();

        if ($presences->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada riwayat presensi untuk user ini.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $presences,
        ]);
    }

    /**
     * Fungsi untuk melihat rekap bulanan (summary).
     */
    public function summary($user_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m', // Format bulan: yyyy-mm
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $month = $request->month;
        $startOfMonth = $month . '-01';
        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

        $presences = Presence::where('user_id', $user_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        if ($presences->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada presensi untuk bulan ini.',
            ], 404);
        }

        $statusCounts = $presences->groupBy('status')->map(function ($group) {
            return $group->count();
        });

        $summary = [
            'hadir' => $statusCounts->get('hadir', 0),
            'tidak hadir' => $statusCounts->get('tidak hadir', 0),
            'izin' => $statusCounts->get('izin', 0),
            'sakit' => $statusCounts->get('sakit', 0),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Rekap presensi bulanan.',
            'data' => $summary,
        ]);
    }

    /**
     * Fungsi untuk analisis presensi (analysis).
     */
    public function analysis(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'required|string|in:user_id,status',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $groupBy = $request->group_by;

        // Query data dengan group_by
        $analysis = Presence::selectRaw("$groupBy as group, COUNT(*) as total")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy($groupBy)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Analisis presensi.',
            'data' => $analysis,
        ]);
    } catch (\Exception $e) {
        // Tampilkan error detail
        return response()->json([
            'status' => false,
            'message' => 'Server Error',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
