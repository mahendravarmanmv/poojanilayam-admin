<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PujariProfile;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\PujariExperience;
use App\Models\Language;
use App\Models\PujariLanguage;
use App\Models\PujariAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminPujariVerificationController extends Controller
{
    private function imageUploadPath(): string
    {
        $path = base_path('../public/images/priests');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return realpath($path) ?: $path;
    }

    private function syncDefaultAvailability(int $pujariProfileId, bool $isActive): void
    {
        if ($isActive) {
            $existing = PujariAvailability::where('pujari_profile_id', $pujariProfileId)->get();

            if ($existing->isEmpty()) {
                for ($day = 0; $day <= 6; $day++) {
                    PujariAvailability::create([
                        'pujari_profile_id' => $pujariProfileId,
                        'day_of_week'       => $day,
                        'availability_type' => 'recurring',
                        'start_time'        => '06:00:00',
                        'end_time'          => '20:00:00',
                        'status'            => 'available',
                    ]);
                }
            } else {
                PujariAvailability::where('pujari_profile_id', $pujariProfileId)
                    ->update(['status' => 'available']);
            }
        } else {
            PujariAvailability::where('pujari_profile_id', $pujariProfileId)
                ->update(['status' => 'unavailable']);
        }
    }

    public function index(Request $request): View
    {
        $query = PujariProfile::with(['user.profile', 'experiences', 'languages.language']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('pujari_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('verification_status', $status);
        }

        $pujaris = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.verifications.pujaris.index', compact('pujaris'));
    }

    public function create(): View
    {
        $languages = Language::orderBy('name')->get();
        return view('admin.verifications.pujaris.create', compact('languages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'required|integer|min:0|max:70',
            'bio' => 'nullable|string|max:2000',
            'specialization' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'languages' => 'nullable|array',
            'languages.*' => 'integer',
            'is_active' => 'nullable',
            'verification_status' => 'required|in:pending,approved,rejected',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // 1. Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(16)),
                'user_type' => 'pujari',
            ]);

            // 2. Upload photo
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $filename = 'priest_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($this->imageUploadPath(), $filename);
                $photoPath = 'images/priests/' . $filename;
            }

            // 3. UserProfile
            $nameParts = explode(' ', trim($validated['name']), 2);
            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'display_name' => $validated['display_name'],
                'profile_photo' => $photoPath,
            ]);

            // 4. PujariProfile
            $pujariNumber = 'PUJ-' . strtoupper(Str::random(6));
            $isActive = $request->boolean('is_active');

            $pujariData = [
                'user_id' => $user->id,
                'pujari_number' => $pujariNumber,
                'display_name' => $validated['display_name'],
                'experience_years' => $validated['experience_years'],
                'bio' => $validated['bio'] ?? null,
                'is_active' => $isActive,
                'profile_status' => 'active',
                'verification_status' => $validated['verification_status'],
            ];

            if (Schema::hasColumn('pujari_profiles', 'phone')) {
                $pujariData['phone'] = $validated['phone'] ?? null;
            }

            $priest = PujariProfile::create($pujariData);

            // 5. Specialization
            if (!empty($validated['specialization'])) {
                PujariExperience::create([
                    'pujari_profile_id' => $priest->id,
                    'title' => $validated['specialization'],
                    'experience_years' => $validated['experience_years'],
                    'is_current' => true,
                ]);
            }

            // 6. Languages sync
            if (!empty($validated['languages'])) {
                foreach ($validated['languages'] as $langId) {
                    PujariLanguage::create([
                        'pujari_profile_id' => $priest->id,
                        'language_id'       => (int) $langId,
                    ]);
                }
            }

            // 7. Seed recurring availability
            $this->syncDefaultAvailability($priest->id, $isActive);
        });

        return redirect()->route('admin.verifications.pujaris.index')
            ->with('success', 'Priest created and synchronized with the frontend directory.');
    }

    public function show($id): View
    {
        $pujari = PujariProfile::with([
            'user.profile',
            'experiences',
            'languages.language',
            'templeAssignments.temple',
            'availabilities'
        ])->findOrFail($id);

        return view('admin.verifications.pujaris.show', compact('pujari'));
    }

    public function edit($id): View
    {
        $pujari = PujariProfile::with(['user.profile', 'experiences', 'languages'])->findOrFail($id);
        $languages = Language::orderBy('name')->get();
        $selectedLanguages = $pujari->languages->pluck('language_id')->map(fn($v) => (int)$v)->toArray();
        $specialization = $pujari->experiences->pluck('title')->first() ?? '';

        $phone = $pujari->phone ?? '';

        return view('admin.verifications.pujaris.edit', compact(
            'pujari',
            'languages',
            'selectedLanguages',
            'specialization',
            'phone'
        ));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $pujari = PujariProfile::findOrFail($id);
        $user = $pujari->user;
        $profile = $user?->profile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($user->id ?? 0),
            'phone' => 'nullable|string|max:20',
            'experience_years' => 'required|integer|min:0|max:70',
            'bio' => 'nullable|string|max:2000',
            'specialization' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'languages' => 'nullable|array',
            'languages.*' => 'integer',
            'is_active' => 'nullable',
            'verification_status' => 'required|in:pending,approved,rejected',
        ]);

        DB::transaction(function () use ($request, $validated, $pujari, $user, $profile) {
            // Update User
            if ($user) {
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }

            // Photo Replacement
            if ($request->hasFile('profile_photo')) {
                if ($profile && $profile->profile_photo) {
                    $oldFile = base_path('../public/' . $profile->profile_photo);
                    if (file_exists($oldFile) && is_file($oldFile)) {
                        @unlink($oldFile);
                    }
                }

                $file = $request->file('profile_photo');
                $filename = 'priest_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($this->imageUploadPath(), $filename);

                if ($profile) {
                    $profile->update([
                        'profile_photo' => 'images/priests/' . $filename,
                        'display_name' => $validated['display_name'],
                    ]);
                } else if ($user) {
                    UserProfile::create([
                        'user_id' => $user->id,
                        'display_name' => $validated['display_name'],
                        'profile_photo' => 'images/priests/' . $filename,
                    ]);
                }
            } else if ($profile) {
                $profile->update(['display_name' => $validated['display_name']]);
            }

            $isActive = $request->boolean('is_active');

            // Update Pujari Profile
            $pujariData = [
                'display_name' => $validated['display_name'],
                'experience_years' => $validated['experience_years'],
                'bio' => $validated['bio'] ?? null,
                'is_active' => $isActive,
                'profile_status' => 'active',
                'verification_status' => $validated['verification_status'],
            ];

            if (Schema::hasColumn('pujari_profiles', 'phone')) {
                $pujariData['phone'] = $validated['phone'] ?? null;
            }

            $pujari->update($pujariData);

            // Specialization
            if (!empty($validated['specialization'])) {
                $firstExp = $pujari->experiences()->first();
                if ($firstExp) {
                    $firstExp->update([
                        'title' => $validated['specialization'],
                        'experience_years' => $validated['experience_years'],
                    ]);
                } else {
                    PujariExperience::create([
                        'pujari_profile_id' => $pujari->id,
                        'title' => $validated['specialization'],
                        'experience_years' => $validated['experience_years'],
                        'is_current' => true,
                    ]);
                }
            }

            // Sync Languages: wipe and re-attach
            $pujari->languages()->delete();
            if (!empty($validated['languages'])) {
                foreach ($validated['languages'] as $langId) {
                    PujariLanguage::create([
                        'pujari_profile_id' => $pujari->id,
                        'language_id'       => (int) $langId,
                    ]);
                }
            }

            // Sync availability
            $this->syncDefaultAvailability($pujari->id, $isActive);
        });

        return redirect()->route('admin.verifications.pujaris.index')
            ->with('success', 'Priest updated successfully.');
    }

    public function updateVerification(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'verification_status' => 'required|in:pending,approved,rejected',
        ]);

        $pujari = PujariProfile::findOrFail($id);
        $pujari->update([
            'verification_status' => $request->input('verification_status'),
            'profile_status' => 'active',
        ]);

        return back()->with('success', 'Priest status updated to ' . ucfirst($request->input('verification_status')));
    }

    public function destroy($id): RedirectResponse
    {
        $pujari = PujariProfile::findOrFail($id);

        DB::transaction(function () use ($pujari) {
            $user = $pujari->user;
            $profile = $user?->profile;

            if ($profile && $profile->profile_photo) {
                $photoPath = base_path('../public/' . $profile->profile_photo);
                if (file_exists($photoPath) && is_file($photoPath)) {
                    @unlink($photoPath);
                }
            }

            $pujari->experiences()->delete();
            $pujari->languages()->delete();
            $pujari->availabilities()->delete();
            $pujari->templeAssignments()->delete();
            $pujari->delete();

            if ($profile) {
                $profile->delete();
            }
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.verifications.pujaris.index')
            ->with('success', 'Priest deleted successfully.');
    }
}