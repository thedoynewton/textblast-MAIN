<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MessageLog;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function getBroadcastedMessagesData(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        // Fetch messages count from the message_log table
        $messagesData = MessageLog::selectRaw("
                CONVERT(date, created_at) as date, 
                COUNT(CASE WHEN status = 'Sent' THEN 1 END) as success_count,
                COUNT(CASE WHEN status = 'Failed' THEN 1 END) as failed_count
            ")
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->when($request->campus, function ($query) use ($request) {
                return $query->where('campus_id', $request->campus); // Filter by campus if selected
            })
            ->groupByRaw('CONVERT(date, created_at)')
            ->orderBy('date')
            ->get();

        // Format data for the chart
        $chartData = [
            'labels' => $messagesData->pluck('date'),
            'success' => $messagesData->pluck('success_count'),
            'failed' => $messagesData->pluck('failed_count')
        ];

        return response()->json($chartData);
    }
}
