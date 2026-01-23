<?php

namespace App\Http\Controllers;

use App\Models\IncomingActivityLog;
use App\Models\QcActivityLog;
use App\Models\ReservationActivityLog;
use App\Models\ReturnActivityLog;
use App\Models\WarehouseActivityLog;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use App\Models\InventoryStock;
use App\Models\SystemError;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get date range filters from request
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
        
        // Default date references
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Helper to apply date range filter
        $applyDateFilter = function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            return $query;
        };

        // Helper to count across all log tables with date filter
        $countLogs = function ($queryCallback) use ($applyDateFilter) {
            return $applyDateFilter(IncomingActivityLog::query())->where($queryCallback)->count() +
                   $applyDateFilter(QcActivityLog::query())->where($queryCallback)->count() +
                   $applyDateFilter(ReservationActivityLog::query())->where($queryCallback)->count() +
                   $applyDateFilter(ReturnActivityLog::query())->where($queryCallback)->count() +
                   $applyDateFilter(WarehouseActivityLog::query())->where($queryCallback)->count() +
                   $applyDateFilter(StockMovement::query())->where($queryCallback)->count() +
                   $applyDateFilter(ActivityLog::query())->where($queryCallback)->count();
        };

        // If date range is set, use that for stats, otherwise use default today/week/month
        if ($startDate && $endDate) {
            $totalInRange = $countLogs(fn($q) => $q);
            $stats = [
                'total_today' => $totalInRange,
                'total_week' => $totalInRange,
                'total_month' => $totalInRange,
            ];
        } else {
            $stats = [
                'total_today' => $countLogs(fn($q) => $q->whereDate('created_at', $today)),
                'total_week' => $countLogs(fn($q) => $q->where('created_at', '>=', $startOfWeek)),
                'total_month' => $countLogs(fn($q) => $q->where('created_at', '>=', $startOfMonth)),
            ];
        }

        // 2. Active Users (Unique users who performed an action)
        $activeUsersQuery = ActivityLog::query();
        if ($startDate && $endDate) {
            $activeUsersQuery->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $activeUsersQuery->whereDate('created_at', $today);
        }
        $activeUsersCount = $activeUsersQuery->distinct('user_id')->count('user_id');
        
        // 3. Module Distribution (Pie Chart) with date filter
        if ($startDate && $endDate) {
            $moduleStats = [
                'Incoming' => IncomingActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'QC' => QcActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'Reservation' => ReservationActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'Return' => ReturnActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'Warehouse' => WarehouseActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
                'Stock Movement' => StockMovement::whereBetween('created_at', [$startDate, $endDate])->count(),
                'Master Data' => ActivityLog::whereBetween('created_at', [$startDate, $endDate])->count(),
            ];
        } else {
            $moduleStats = [
                'Incoming' => IncomingActivityLog::count(),
                'QC' => QcActivityLog::count(),
                'Reservation' => ReservationActivityLog::count(),
                'Return' => ReturnActivityLog::count(),
                'Warehouse' => WarehouseActivityLog::count(),
                'Stock Movement' => StockMovement::count(),
                'Master Data' => ActivityLog::count(),
            ];
        }

        // 4. Time-based Activity Chart
        // Without filter: Hourly activity (last 24 hours)
        // With filter: Daily activity per day in the range
        $chartMode = 'hourly'; // 'hourly' or 'daily'
        $chartLabels = [];
        $chartData = [];
        
        if ($startDate && $endDate) {
            // Daily mode: Group by date
            $chartMode = 'daily';
            $dailyQuery = ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();
            
            // Generate all dates in range
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->toDateString();
                $chartLabels[] = $currentDate->format('d/m');
                $chartData[] = $dailyQuery[$dateStr] ?? 0;
                $currentDate->addDay();
            }
        } else {
            // Hourly mode: Last 24 hours
            $hourlyStats = ActivityLog::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();
            
            // Fill missing hours with 0
            for ($i = 0; $i < 24; $i++) {
                $chartLabels[] = sprintf('%02d:00', $i);
                $chartData[] = $hourlyStats[$i] ?? 0;
            }
        }

        // 5. Top Users (Bar Chart) with date filter
        $topUsersQuery = ActivityLog::select('activity_logs.user_id', 'users.name', DB::raw('count(*) as total'))
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id');
        if ($startDate && $endDate) {
            $topUsersQuery->whereBetween('activity_logs.created_at', [$startDate, $endDate]);
        }
        $topUsers = $topUsersQuery
            ->groupBy('activity_logs.user_id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'name' => $log->name ?? 'Unknown',
                    'count' => $log->total
                ];
            });

        // 6. Online Users (Last 5 minutes) - not affected by date filter
        $onlineUsers = \App\Models\User::where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
            ->orderByDesc('last_seen_at')
            ->get(['id', 'name', 'role_id', 'last_seen_at', 'email']);

        // 7. Recent Activities Feed (Merged from all sources) with date filter
        $fetchLatest = function($model) use ($startDate, $endDate) {
            $query = $model::with(['user:id,name', 'material:id,kode_item,nama_material']);
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            return $query->latest()->limit(5)->get();
        };

        $recentLogs = collect()
            ->concat($fetchLatest(IncomingActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Incoming')))
            ->concat($fetchLatest(QcActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'QC')))
            ->concat($fetchLatest(ReservationActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Reservation')))
            ->concat($fetchLatest(ReturnActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Return')))
            ->concat($fetchLatest(WarehouseActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Warehouse')))
            ->concat($fetchLatest(StockMovement::class)->map(fn($l) => $this->formatLogForFeed($l, 'Stock Movement')))
            ->concat($fetchLatest(ActivityLog::class)->map(fn($l) => $this->formatLogForFeed($l, 'Master Data')))
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        // 8. Expired Materials Logic (Ported from DashboardController)
        $validStatuses = ['KARANTINA', 'RELEASED', 'HOLD', 'REJECTED'];
        $expiredMaterials = InventoryStock::with(['material', 'bin'])
            ->where('qty_on_hand', '>', 0)
            ->where('exp_date', '<=', now())
            ->whereIn('status', $validStatuses)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->material->nama_material ?? 'Unknown',
                'expiry_date' => $item->exp_date ? $item->exp_date->toDateString() : 'N/A',
            ]);

        $expiredCount = $expiredMaterials->count();

        // 9. Alerts Logic (Basic version for IT Dashboard)
        $alerts = [];
        if ($expiredCount > 0) {
            $alerts[] = [
                'id' => 'expired_alert',
                'message' => "Ada {$expiredCount} item yang sudah expired. Segera cek inventori.",
                'created_at' => now()->toDateTimeString(),
            ];
        }

        // 10. System Errors & Critical Alerts
        $criticalErrors = SystemError::where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        foreach ($criticalErrors as $error) {
            $alerts[] = [
                'id' => 'error_' . $error->id,
                'message' => "[{$error->type}] {$error->message}",
                'created_at' => $error->created_at->toDateTimeString(),
                'severity' => 'critical',
            ];
        }

        // 11. Error Rate Chart Data (matching chartMode)
        $errorChartData = [];
        if ($chartMode === 'daily') {
            $dailyErrors = SystemError::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->toDateString();
                $errorChartData[] = $dailyErrors[$dateStr] ?? 0;
                $currentDate->addDay();
            }
        } else {
            $hourlyErrors = SystemError::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->groupBy('hour')
                ->pluck('count', 'hour')
                ->toArray();
            
            for ($i = 0; $i < 24; $i++) {
                $errorChartData[] = $hourlyErrors[$i] ?? 0;
            }
        }

        return Inertia::render('ITDashboard', [
            'stats' => $stats,
            'activeUsers' => $activeUsersCount,
            'onlineUsers' => $onlineUsers,
            'moduleStats' => $moduleStats,
            'chartMode' => $chartMode,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'errorChartData' => $errorChartData,
            'topUsers' => $topUsers,
            'recentActivities' => $recentLogs,
            'expiredMaterials' => $expiredMaterials,
            'expiredMaterialsCount' => $expiredCount,
            'localAlerts' => $alerts,
        ]);
    }

    private function formatLogForFeed($log, $module)
    {
        // Handle StockMovement which might use 'executedBy' instead of 'user'
        $user = $log->user ?? $log->executedBy ?? null;
        $userName = $user ? ($user->name ?? $user->name ?? 'System') : 'System';
        
        // Handle Action/Description
        $action = $log->action ?? $log->movement_type ?? 'Unknown';
        $desc = $log->description ?? $log->remarks ?? '';

        if ($module === 'Stock Movement') {
            $desc = "{$log->qty} {$log->uom} moved";
        }
        
        return [
            'id' => $log->id,
            'module' => $module,
            'user' => $userName,
            'action' => $action,
            'description' => $desc,
            'created_at' => $log->created_at->diffForHumans(), // Human readable time
            'timestamp' => $log->created_at, // For sorting
        ];

    }

    public function index()
    {
        $user = auth()->user();
        $filterUserId = null;

        // Jika TIDAK punya view_all tapi punya view_self, filter berdasarkan user ID
        if (!$user->hasPermission('activity_log.view_all') && $user->hasPermission('activity_log.view_self')) {
            $filterUserId = $user->id;
        }
        // Jika tidak punya keduanya (should be handled by middleware/sidebar), defaultnya kosong atau error.
        // Asumsi middleware sudah handle akses dasar.

        $queryCallback = function ($query) use ($filterUserId) {
            if ($filterUserId) {
                $query->where('user_id', $filterUserId);
            }
        };

        $incomingLogs = IncomingActivityLog::with(['user.role', 'material'])->tap($queryCallback)->get();
        $qcLogs = QcActivityLog::with(['user.role', 'material'])->tap($queryCallback)->get();
        $reservationLogs = ReservationActivityLog::with(['user.role', 'material'])->tap($queryCallback)->get();
        $returnLogs = ReturnActivityLog::with(['user.role', 'material'])->tap($queryCallback)->get();
        $warehouseLogs = WarehouseActivityLog::with(['user.role', 'material'])->tap($queryCallback)->get();
        
        $stockMovementLogs = StockMovement::with([
            'executedBy.role', // Ambil user dan role
            'material',
            'fromBin',
            'toBin',
        ])
        ->when($filterUserId, function ($q) use ($filterUserId) {
            $q->where('executed_by', $filterUserId);
        })
        ->orderBy('movement_date', 'desc')
        ->get();

        // Tambahkan Master Data Activity Logs
        $masterDataLogs = ActivityLog::with([
            'user.role', 
            'material', 
            'supplier', 
            'warehouseBin.zone'
        ])->tap($queryCallback)->get();

        $activities = collect([])
            ->concat($this->mapIncomingLogs($incomingLogs))
            ->concat($this->mapQcLogs($qcLogs))
            ->concat($this->mapReservationLogs($reservationLogs))
            ->concat($this->mapReturnLogs($returnLogs))
            ->concat($this->mapWarehouseLogs($warehouseLogs))
            // --- BARU: Gabungkan StockMovement Logs ---
            ->concat($this->mapStockMovementLogs($stockMovementLogs))
            // ----------------------------------------
            ->concat($this->mapMasterDataLogs($masterDataLogs))
            ->sortByDesc('timestamp')
            ->values();

        return Inertia::render('RiwayatAktivitas', [
            'activities' => $activities,
        ]);
    }

    private function mapStockMovementLogs($logs)
    {
        return $logs->map(function ($log) {
            
            // Tentukan aksi dan deskripsi yang lebih detail
            $action = $log->movement_type;
            $binFromCode = $log->fromBin->bin_code ?? 'N/A';
            $binToCode = $log->toBin->bin_code ?? 'N/A';
            
            if ($log->movement_type === 'B2B') {
                $description = "Transfer Bin-to-Bin: Pindah {$log->qty} {$log->uom} dari {$binFromCode} ke {$binToCode}.";
            } elseif ($log->movement_type === 'GR') {
                $description = "Penerimaan Barang (GR): Stok {$log->qty} {$log->uom} masuk ke {$binToCode}.";
            } elseif ($log->movement_type === 'PUTAWAY') {
                $description = "Putaway: Stok {$log->qty} {$log->uom} dipindahkan dari {$binFromCode} ke lokasi penyimpanan permanen {$binToCode}.";
            } else {
                $description = "Pergerakan Stok {$log->movement_number}: Tipe {$log->movement_type} ({$log->qty} {$log->uom}).";
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->movement_date->toDateTimeString(),
                'user' => $log->executedBy->name ?? 'System',
                'role' => $log->executedBy->role->role_name ?? 'N/A',
                'module' => 'Warehouse - Stock Movement', // Kategori yang jelas
                'action' => $action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot ?? '-',
                'qty_before' => $log->qty_before ?? 0, // StockMovement mungkin tidak punya qty_before/after
                'qty_after' => $log->qty, // Menggunakan qty sebagai qty_after movement
                'bin_from' => $binFromCode,
                'bin_to' => $binToCode,
                'reference_no' => $log->reference_type . ' ' . $log->movement_number,
                'device' => 'N/A (Backend Log)',
                'ip_address' => 'N/A',
                'remarks' => $description, // Menggunakan deskripsi yang dibuat
                'exp_date' => null, // StockMovement mungkin tidak mencatat exp_date
                'old_value' => null,
                'new_value' => null,
            ];
        });
    }

    private function mapIncomingLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Incoming Goods',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapQcLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Quality Control',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapReservationLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Reservation',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapReturnLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Return',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapWarehouseLogs($logs)
    {
        return $logs->map(function ($log) {
            // Convert bin_from and bin_to from ID to bin_code if numeric
            $binFrom = $log->bin_from;
            $binTo = $log->bin_to;
            
            if (is_numeric($binFrom)) {
                $bin = \App\Models\WarehouseBin::find($binFrom);
                $binFrom = $bin ? $bin->bin_code : $binFrom;
            }
            
            if (is_numeric($binTo)) {
                $bin = \App\Models\WarehouseBin::find($binTo);
                $binTo = $bin ? $bin->bin_code : $binTo;
            }
            
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => 'Warehouse',
                'action' => $log->action,
                'sku_code' => $log->material->kode_item ?? 'N/A',
                'sku_name' => $log->material->nama_material ?? 'N/A',
                'lot_no' => $log->batch_lot,
                'qty_before' => $log->qty_before,
                'qty_after' => $log->qty_after,
                'bin_from' => $binFrom,
                'bin_to' => $binTo,
                'reference_no' => $log->reference_document,
                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
            ];
        });
    }

    private function mapMasterDataLogs($logs)
    {
        return $logs->map(function ($log) {
            // Tentukan module berdasarkan foreign key yang terisi
            $module = 'Master Data';
            $entityCode = 'N/A';
            $entityName = 'N/A';

            if (str_contains($log->action, 'Putaway') || str_contains($log->action, 'TO')) {
                $module = 'Warehouse - Stock Movement';
                
                // Ambil detail material jika ada
                if ($log->material_id && $log->material) {
                    $entityCode = $log->material->kode_item;
                    $entityName = $log->material->nama_material;
                } else {
                    // Jika tidak ada material, ambil dari deskripsi
                    $entityName = 'Transfer Order';
                }
            }

            elseif ($log->material_id && $log->material) {
                $module = 'Master Data - SKU/Material';
                $entityCode = $log->material->kode_item;
                $entityName = $log->material->nama_material;
            } elseif ($log->supplier_id && $log->supplier) {
                $module = 'Master Data - Supplier';
                $entityCode = $log->supplier->kode_supplier;
                $entityName = $log->supplier->nama_supplier;
            } elseif ($log->warehousebin_id && $log->warehouseBin) {
                $module = 'Master Data - Bin Location';
                $entityCode = $log->warehouseBin->bin_code;
                $entityName = $log->warehouseBin->bin_name . ' (' . ($log->warehouseBin->zone->zone_name ?? 'N/A') . ')';
            } elseif ($log->user_id_target) {
                $module = 'Master Data - User';
                // Parse dari description atau old/new value
                $description = $log->description ?? '';
                if (preg_match('/User: (.+)/', $description, $matches)) {
                    $entityName = $matches[1];
                }
            }

            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->toDateTimeString(),
                'user' => $log->user->name ?? 'System',
                'role' => $log->user->role->role_name ?? 'N/A',
                'module' => $module,
                'action' => $log->action,
                'sku_code' => $entityCode,
                'sku_name' => $entityName,

                'lot_no' => $log->batch_lot ?? '-',
                'qty_before' => $log->qty_before ?? 0,
                'qty_after' => $log->qty_after ?? 0,
                'bin_from' => $log->bin_from ?? '-',
                'bin_to' => $log->bin_to ?? '-',
                'reference_no' => $log->reference_document ?? '-',

                'device' => $log->device_info,
                'ip_address' => $log->ip_address,
                'remarks' => $log->description,
                'exp_date' => $log->exp_date,
                'old_value' => $log->old_value,
                'new_value' => $log->new_value,
            ];
        });
    }
    public function resolveError($id)
    {
        $error = SystemError::findOrFail($id);
        $error->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Error berhasil ditandai sebagai selesai.');
    }
}