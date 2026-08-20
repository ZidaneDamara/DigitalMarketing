<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = AuditLog::query();

            return DataTables::of($logs->latest('created_at'))
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i:s'))
                ->editColumn('action', function ($row) {
                    $badge = match ($row->action) {
                        'CREATE' => 'bg-success',
                        'UPDATE' => 'bg-warning text-dark',
                        'DELETE' => 'bg-danger',
                        'LOGIN' => 'bg-info text-dark',
                        'LOGOUT' => 'bg-secondary',
                        default => 'bg-primary',
                    };
                    return '<span class="badge ' . $badge . '">' . $row->action . '</span>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.audit_logs.index');
    }
}
