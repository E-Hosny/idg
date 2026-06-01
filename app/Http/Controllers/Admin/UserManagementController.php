<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => $this->userPayload($user));

        return Inertia::render('Dashboard/Users/Index', [
            'users' => $users,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'notification_email' => $this->normalizeNotificationEmail($validated['notification_email'] ?? null),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'تم إنشاء المستخدم بنجاح | User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUser($request, $user, requirePassword: false);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->notification_email = $this->normalizeNotificationEmail($validated['notification_email'] ?? null);
        $user->role = $validated['role'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'تم تحديث المستخدم بنجاح | User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي | You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'تم حذف المستخدم | User deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'notification_email' => $user->notification_email,
            'effective_notification_email' => $user->effectiveNotificationEmail(),
            'uses_login_email_for_notifications' => $user->usesLoginEmailForNotifications(),
            'role' => $user->role,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return list<array{value: string, label_en: string, label_ar: string}>
     */
    private function roleOptions(): array
    {
        return [
            ['value' => 'admin', 'label_en' => 'Admin', 'label_ar' => 'مدير'],
            ['value' => 'receptionist', 'label_en' => 'Reception', 'label_ar' => 'استقبال'],
            ['value' => 'lab', 'label_en' => 'Lab', 'label_ar' => 'معمل'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, ?User $user = null, bool $requirePassword = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'receptionist', 'lab'])],
        ];

        if ($requirePassword) {
            $rules['password'] = ['required', 'string', Password::defaults()];
        } elseif ($request->filled('password')) {
            $rules['password'] = ['string', Password::defaults()];
        }

        return $request->validate($rules, [
            'name.required' => 'الاسم مطلوب | Name is required.',
            'email.required' => 'البريد مطلوب | Email is required.',
            'email.unique' => 'البريد مستخدم مسبقاً | Email already in use.',
            'role.required' => 'الدور مطلوب | Role is required.',
            'password.required' => 'كلمة المرور مطلوبة | Password is required.',
        ]);
    }

    private function normalizeNotificationEmail(?string $email): ?string
    {
        $email = $email !== null ? trim($email) : null;

        return $email === '' ? null : $email;
    }
}
