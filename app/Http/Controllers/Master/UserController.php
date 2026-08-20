<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Branch;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with(['branch', 'roles']);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role_name', function ($row) {
                    $role = $row->roles->first()?->name ?? '-';
                    $badge = match($role) {
                        'Super Admin' => 'bg-danger',
                        'Area Manager' => 'bg-info',
                        default => 'bg-primary',
                    };
                    return '<span class="badge ' . $badge . '">' . $role . '</span>';
                })
                ->addColumn('nama_cabang', function ($row) {
                    return $row->branch ? $row->branch->nama_cabang : '<span class="text-muted">Semua Cabang</span>';
                })
                ->editColumn('status', function ($row) {
                    $badge = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-outline-warning btn-edit" data-id="' . $row->id . '"><i class="fas fa-edit"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['role_name', 'nama_cabang', 'status', 'action'])
                ->make(true);
        }

        $branches = Branch::where('status', 'active')->get();
        $roles = Role::all();

        return view('master.users.index', compact('branches', 'roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($data['role']);

        AuditLogService::log('CREATE', 'Master User', "Menambahkan user baru: {$user->name} ({$user->email})");

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data' => $user,
        ]);
    }

    public function show(User $user)
    {
        $user->load('roles');
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'branch_id' => $user->branch_id,
            'role' => $user->roles->first()?->name,
            'status' => $user->status,
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$data['role']]);

        AuditLogService::log('UPDATE', 'Master User', "Memperbarui user: {$user->name} ({$user->email})");

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.',
            'data' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        AuditLogService::log('DELETE', 'Master User', "Menghapus user: {$name}");

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
