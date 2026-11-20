<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminRole;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminManagementController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Admins';
        $admins = Admin::orderByRaw("FIELD(role, 'super_admin') DESC")
            ->orderBy('created_at')
            ->paginate(getPaginate());

        $roles = AdminRole::LABELS;
        return view('admin.admins.index', compact('pageTitle', 'admins', 'roles'));
    }

    public function create()
    {
        $pageTitle = 'Create Admin';
        $roles     = AdminRole::LABELS;
        return view('admin.admins.create', compact('pageTitle', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:40',
            'email'    => 'required|email|unique:admins,email',
            'username' => 'required|string|min:3|max:25|unique:admins,username|regex:/^[a-zA-Z0-9_]+$/',
            'role'     => 'required|in:' . implode(',', array_keys(AdminRole::LABELS)),
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($request->role === AdminRole::SUPER_ADMIN) {
            $notify[] = ['error', 'You cannot create another Super Admin account.'];
            return back()->withNotify($notify)->withInput();
        }

        $plainPassword   = $request->password;
        $creator         = Auth::guard('admin')->user();

        $admin           = new Admin();
        $admin->name     = $request->name;
        $admin->email    = $request->email;
        $admin->username = $request->username;
        $admin->role     = $request->role;
        $admin->password = Hash::make($plainPassword);
        $admin->status   = 1;
        $admin->save();

        // ── 1. Email to the newly created admin ──────────────────────────────
        try {
            notify(
                $admin,
                'ADMIN_CREATED',
                [
                    'name'      => $admin->name,
                    'username'  => $admin->username,
                    'role'      => $admin->roleLabel,
                    'password'  => $plainPassword,
                    'login_url' => route('admin.login'),
                ],
                ['email'],
                false
            );
        } catch (\Exception $e) {
            logger()->error('Admin welcome email failed: ' . $e->getMessage(), ['exception' => $e]);
        }

        // ── 2. Email + notification to every Super Admin ─────────────────────
        try {
            $superAdmins = Admin::where('role', AdminRole::SUPER_ADMIN)->where('status', 1)->get();

            foreach ($superAdmins as $superAdmin) {
                notify(
                    $superAdmin,
                    'ADMIN_CREATED_ALERT',
                    [
                        'super_admin_name' => $superAdmin->name,
                        'name'             => $admin->name,
                        'username'         => $admin->username,
                        'email'            => $admin->email,
                        'role'             => $admin->roleLabel,
                        'created_by'       => $creator->name,
                        'created_at'       => now()->format('d M Y H:i'),
                        'admin_url'        => route('admin.admins.index'),
                    ],
                    ['email'],
                    false
                );

                AdminNotification::create([
                    'title'        => 'New admin account created: ' . $admin->name . ' (' . $admin->roleLabel . ')',
                    'click_url'    => route('admin.admins.index'),
                    'module'       => 'manage_admins',
                    'triggered_by' => $creator->id,
                ]);
            }
        } catch (\Exception $e) {
            logger()->error('Admin creation notifications failed: ' . $e->getMessage(), ['exception' => $e]);
        }

        $notify[] = ['success', 'Admin account created successfully. Login credentials have been emailed.'];
        return to_route('admin.admins.index')->withNotify($notify);
    }

    public function edit(int $id)
    {
        $admin     = Admin::findOrFail($id);
        $pageTitle = 'Edit Admin — ' . $admin->name;
        $roles     = AdminRole::LABELS;

        if ($admin->isSuperAdmin() && $admin->id !== Auth::guard('admin')->id()) {
            $notify[] = ['error', 'You cannot edit this Super Admin account.'];
            return to_route('admin.admins.index')->withNotify($notify);
        }

        return view('admin.admins.edit', compact('pageTitle', 'admin', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:40',
            'email'    => 'required|email|unique:admins,email,' . $id,
            'username' => 'required|string|min:3|max:25|unique:admins,username,' . $id . '|regex:/^[a-zA-Z0-9_]+$/',
            'role'     => 'required|in:' . implode(',', array_keys(AdminRole::LABELS)),
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        /** @var Admin $currentAdmin */
        $currentAdmin = Auth::guard('admin')->user();
        if ($admin->id === $currentAdmin->id && $request->role !== AdminRole::SUPER_ADMIN) {
            $notify[] = ['error', 'You cannot change your own role.'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->role === AdminRole::SUPER_ADMIN && !$admin->isSuperAdmin()) {
            $notify[] = ['error', 'You cannot assign the Super Admin role.'];
            return back()->withNotify($notify)->withInput();
        }

        $admin->name     = $request->name;
        $admin->email    = $request->email;
        $admin->username = $request->username;
        $admin->role     = $request->role;
        $admin->status   = $request->status ? 1 : 0;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        $notify[] = ['success', 'Admin account updated successfully.'];
        return to_route('admin.admins.index')->withNotify($notify);
    }

    public function toggleStatus(int $id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === Auth::guard('admin')->id()) {
            $notify[] = ['error', 'You cannot deactivate your own account.'];
            return back()->withNotify($notify);
        }

        if ($admin->isSuperAdmin()) {
            $notify[] = ['error', 'The Super Admin account cannot be deactivated.'];
            return back()->withNotify($notify);
        }

        $admin->status = $admin->status == 1 ? 0 : 1;
        $admin->save();

        $label    = $admin->status == 1 ? 'activated' : 'deactivated';
        $notify[] = ['success', "Admin account {$label} successfully."];
        return back()->withNotify($notify);
    }

    public function delete(int $id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === Auth::guard('admin')->id()) {
            $notify[] = ['error', 'You cannot delete your own account.'];
            return back()->withNotify($notify);
        }

        if ($admin->isSuperAdmin()) {
            $notify[] = ['error', 'The Super Admin account cannot be deleted.'];
            return back()->withNotify($notify);
        }

        $admin->delete();

        $notify[] = ['success', 'Admin account deleted successfully.'];
        return to_route('admin.admins.index')->withNotify($notify);
    }
}
