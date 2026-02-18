<?php

namespace App\Http\Controllers;

use App\Models\Analytics\SalesAnalytics;
use App\Models\Analytics\InventoryAnalytics;
use App\Models\Analytics\StaffAnalytics;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $salesData = SalesAnalytics::getDailySales($startDate, $endDate);
        $topItems = SalesAnalytics::getTopSellingItems($startDate, $endDate, 5);
        $customerAnalytics = SalesAnalytics::getCustomerAnalytics($startDate, $endDate);
        $revenueByType = SalesAnalytics::getRevenueByOrderType($startDate, $endDate);
        $peakHours = SalesAnalytics::getPeakHours($startDate, $endDate);

        return view('analytics.dashboard', compact(
            'salesData', 'topItems', 'customerAnalytics',
            'revenueByType', 'peakHours', 'startDate', 'endDate'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();
        $period = $request->get('period', 'daily');

        $salesData = SalesAnalytics::getDailySales($startDate, $endDate);
        $topItems = SalesAnalytics::getTopSellingItems($startDate, $endDate, 20);
        $customerAnalytics = SalesAnalytics::getCustomerAnalytics($startDate, $endDate);
        $revenueByType = SalesAnalytics::getRevenueByOrderType($startDate, $endDate);
        $peakHours = SalesAnalytics::getPeakHours($startDate, $endDate);
        $seasonalTrends = SalesAnalytics::getSeasonalTrends();

        // Calculate growth rates
        $previousPeriodStart = $startDate->copy()->subDays($startDate->diffInDays($endDate) + 1);
        $previousPeriodEnd = $startDate->copy()->subDay();
        $previousSales = SalesAnalytics::getDailySales($previousPeriodStart, $previousPeriodEnd);

        $currentRevenue = $salesData->sum('revenue');
        $previousRevenue = $previousSales->sum('revenue');
        $growthRate = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        return view('analytics.sales', compact(
            'salesData', 'topItems', 'customerAnalytics', 'revenueByType',
            'peakHours', 'seasonalTrends', 'growthRate', 'startDate', 'endDate'
        ));
    }

    public function inventory()
    {
        $stockLevels = InventoryAnalytics::getStockLevels();
        $inventoryTurnover = InventoryAnalytics::getInventoryTurnover(now()->startOfMonth(), now());
        $wastageAnalysis = InventoryAnalytics::getWastageAnalysis(now()->startOfMonth(), now());
        $inventoryValue = InventoryAnalytics::getInventoryValue();
        $demandForecast = InventoryAnalytics::getForecastDemand();

        return view('analytics.inventory', compact(
            'stockLevels', 'inventoryTurnover', 'wastageAnalysis',
            'inventoryValue', 'demandForecast'
        ));
    }

    public function staff(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        $staffPerformance = StaffAnalytics::getStaffPerformance($startDate, $endDate);
        $shiftAnalysis = StaffAnalytics::getShiftAnalysis($startDate, $endDate);
        $efficiencyMetrics = StaffAnalytics::getEfficiencyMetrics($startDate, $endDate);

        return view('analytics.staff', compact(
            'staffPerformance', 'shiftAnalysis', 'efficiencyMetrics', 'startDate', 'endDate'
        ));
    }

    public function predictive()
    {
        $demandForecast = $this->generateDemandForecast();
        $salesForecast = $this->generateSalesForecast();
        $inventoryRecommendations = $this->generateInventoryRecommendations();
        $staffingRecommendations = $this->generateStaffingRecommendations();

        return view('analytics.predictive', compact(
            'demandForecast', 'salesForecast', 'inventoryRecommendations', 'staffingRecommendations'
        ));
    }

    public function realtime()
    {
        $todaySales = SalesAnalytics::getHourlySales();
        $currentOrders = Order::whereDate('created_at', today())
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->with(['orderItems.menuItem', 'table'])
            ->get();

        $liveMetrics = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereDate('created_at', today())->where('status', 'completed')->sum('total_amount'),
            'avg_order_value' => Order::whereDate('created_at', today())->where('status', 'completed')->avg('total_amount'),
            'active_tables' => \App\Models\Table::where('status', 'occupied')->count(),
        ];

        return view('analytics.realtime', compact('todaySales', 'currentOrders', 'liveMetrics'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'csv');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        switch ($type) {
            case 'sales':
                return $this->exportSalesData($startDate, $endDate, $format);
            case 'inventory':
                return $this->exportInventoryData($format);
            case 'staff':
                return $this->exportStaffData($startDate, $endDate, $format);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }

    private function generateDemandForecast()
    {
        // Simple linear regression for demand forecasting
        $historicalData = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(90))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $forecast = [];
        $trend = $this->calculateTrend($historicalData->pluck('orders')->toArray());

        for ($i = 1; $i <= 7; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $predictedOrders = max(0, $historicalData->avg('orders') + ($trend * $i));
            $forecast[] = [
                'date' => $date,
                'predicted_orders' => round($predictedOrders),
                'confidence' => $this->calculateConfidence($historicalData->pluck('orders')->toArray())
            ];
        }

        return $forecast;
    }

    private function generateSalesForecast()
    {
        $historicalRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(90))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $forecast = [];
        $trend = $this->calculateTrend($historicalRevenue->pluck('revenue')->toArray());

        for ($i = 1; $i <= 30; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $predictedRevenue = max(0, $historicalRevenue->avg('revenue') + ($trend * $i));
            $forecast[] = [
                'date' => $date,
                'predicted_revenue' => round($predictedRevenue, 2)
            ];
        }

        return $forecast;
    }

    private function generateInventoryRecommendations()
    {
        return InventoryAnalytics::getForecastDemand(30);
    }

    private function generateStaffingRecommendations()
    {
        $peakHours = SalesAnalytics::getPeakHours(now()->subDays(30), now());
        $recommendations = [];

        foreach ($peakHours as $hour) {
            if ($hour->order_count > 10) { // Threshold for busy hours
                $recommendations[] = [
                    'hour' => $hour->hour,
                    'recommended_staff' => ceil($hour->order_count / 5), // 1 staff per 5 orders
                    'reason' => "Peak hour with {$hour->order_count} orders"
                ];
            }
        }

        return $recommendations;
    }

    private function calculateTrend($data)
    {
        $n = count($data);
        if ($n < 2) return 0;

        $sumX = array_sum(range(1, $n));
        $sumY = array_sum($data);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;
            $y = $data[$i];
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        return ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    }

    private function calculateConfidence($data)
    {
        $mean = array_sum($data) / count($data);
        $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $data)) / count($data);
        $stdDev = sqrt($variance);

        // Simple confidence calculation (higher std dev = lower confidence)
        return max(0, min(100, 100 - ($stdDev / $mean) * 100));
    }

    private function exportSalesData($startDate, $endDate, $format)
    {
        $data = SalesAnalytics::getDailySales($startDate, $endDate);

        if ($format === 'csv') {
            $filename = 'sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Revenue', 'Orders', 'Average Order Value']);

                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->date,
                        $row->revenue,
                        $row->orders,
                        $row->avg_order_value
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Add other format exports (PDF, Excel) here
        return redirect()->back()->with('error', 'Export format not supported');
    }

    private function exportInventoryData($format)
    {
        // Implementation for inventory data export
        return redirect()->back()->with('error', 'Inventory export not implemented');
    }

    private function exportStaffData($startDate, $endDate, $format)
    {
        // Implementation for staff data export
        return redirect()->back()->with('error', 'Staff export not implemented');
    }
}
