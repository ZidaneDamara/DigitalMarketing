<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $branches = Branch::query();

            return DataTables::of($branches)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $badgeClass = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-outline-warning btn-edit" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('master.branches.index');
    }

    public function store(BranchRequest $request)
    {
        $branch = Branch::create($request->validated());

        AuditLogService::log('CREATE', 'Master Cabang', "Menambahkan cabang baru: {$branch->nama_cabang} ({$branch->kode})");

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil ditambahkan.',
            'data' => $branch,
        ]);
    }

    public function show(Branch $branch)
    {
        return response()->json($branch);
    }

    public function update(BranchRequest $request, Branch $branch)
    {
        $branch->update($request->validated());

        AuditLogService::log('UPDATE', 'Master Cabang', "Memperbarui data cabang: {$branch->nama_cabang} ({$branch->kode})");

        return response()->json([
            'success' => true,
            'message' => 'Data cabang berhasil diperbarui.',
            'data' => $branch,
        ]);
    }

    public function destroy(Branch $branch)
    {
        $nama = $branch->nama_cabang;
        $branch->delete();

        AuditLogService::log('DELETE', 'Master Cabang', "Menghapus cabang: {$nama}");

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil dihapus.',
        ]);
    }
}
