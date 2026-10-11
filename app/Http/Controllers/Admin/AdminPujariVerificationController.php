<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PujariProfile;
use App\Models\PujariVerification;
use App\Models\User;
use App\Models\Role;
use App\Models\Temple;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPujariVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = PujariProfile::with(['user', 'documents', 'bankAccount']);

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $pujaris = $query->latest()->paginate(15)->withQueryString();

        return view('admin.verifications.pujaris.index', compact('pujaris'));
    }

    public function create()
    {
        $temples = Temple::all();
        $languages = Language::all();
        return view('admin.verifications.pujaris.create', compact('temples', 'languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'mobile'               => 'required|string|unique:users,mobile',
            'password'             => 'required|string|min:8',
            'experience_years'     => 'required|integer|min:0',
            'about'                => 'nullable|string',
            'verification_status' => 'required|in:pending,verified,approved,rejected,suspended',
            'temple_id'            => 'nullable|exists:temples,id',
            'languages'            => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create Base User Account
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'mobile'   => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'status'   => 'active',
            ]);

            // Assign Priest/Pujari Role if exists
            $pujariRole = Role::where('slug', 'pujari')->first();
            if ($pujariRole) {
                $user->roles()->syncWithoutDetaching([$pujariRole->id]);
            }

            // 2. Create Pujari Profile
            $profile = PujariProfile::create([
                'user_id'             => $user->id,
                'experience_years'    => $validated['experience_years'],
                'about'               => $validated['about'] ?? null,
                'verification_status' => $validated['verification_status'],
            ]);

            // 3. Assign to Temple if selected
            if (!empty($validated['temple_id'])) {
                $profile->templeAssignments()->create([
                    'temple_id'   => $validated['temple_id'],
                    'status'      => 'active',
                    'assigned_at' => now(),
                ]);
            }

            // 4. Record Verification Log if created as verified
            if ($validated['verification_status'] === 'verified') {
                PujariVerification::create([
                    'pujari_profile_id' => $profile->id,
                    'verified_by'       => auth()->id(),
                    'status'            => 'verified',
                    'remarks'           => 'Verified upon profile creation by Administrator',
                    'verified_at'       => now(),
                ]);
            }
        });

        return redirect()->route('admin.verifications.pujaris.index')->with('success', 'Priest created successfully.');
    }

    public function show(PujariProfile $pujari)
    {
        $pujari->load(['user', 'documents', 'bankAccount', 'languages', 'experiences', 'templeAssignments.temple']);
        return view('admin.verifications.pujaris.show', compact('pujari'));
    }

    public function edit(PujariProfile $pujari)
    {
        $pujari->load(['user', 'templeAssignments']);
        $temples = Temple::all();
        return view('admin.verifications.pujaris.edit', compact('pujari', 'temples'));
    }

    public function update(Request $request, PujariProfile $pujari)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email,' . $pujari->user_id,
            'mobile'              => 'required|string|unique:users,mobile,' . $pujari->user_id,
            'password'            => 'nullable|string|min:8',
            'experience_years'    => 'required|integer|min:0',
            'about'               => 'nullable|string',
            'verification_status' => 'required|in:pending,approved,verified,rejected,suspended',
            'temple_id'           => 'nullable|exists:temples,id',
        ]);

        DB::transaction(function () use ($pujari, $validated) {
            $userData = [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'mobile' => $validated['mobile'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $pujari->user->update($userData);

            // Normalize 'verified' to 'approved' for frontend consistency
            $status = ($validated['verification_status'] === 'verified') ? 'approved' : $validated['verification_status'];

            $pujari->update([
                'experience_years'    => $validated['experience_years'],
                'about'               => $validated['about'] ?? null,
                'verification_status' => $status,
            ]);

            if (!empty($validated['temple_id'])) {
                $pujari->templeAssignments()->updateOrCreate(
                    ['pujari_profile_id' => $pujari->id],
                    ['temple_id' => $validated['temple_id'], 'status' => 'active']
                );
            }
        });

        return redirect()->route('admin.verifications.pujaris.index')->with('success', 'Priest profile updated successfully.');
    }

    public function updateVerification(Request $request, PujariProfile $pujari)
    {
        $validated = $request->validate([
            'status'  => 'required|in:approved,verified,rejected,suspended,pending',
            'remarks' => 'required|string|max:1000',
        ]);

        $status = ($validated['status'] === 'verified') ? 'approved' : $validated['status'];

        DB::transaction(function () use ($pujari, $status, $validated) {
            $pujari->update(['verification_status' => $status]);

            PujariVerification::create([
                'pujari_profile_id' => $pujari->id,
                'verified_by'       => auth()->id(),
                'status'            => $status,
                'remarks'           => $validated['remarks'],
                'verified_at'       => now(),
            ]);
        });

        return redirect()->route('admin.verifications.pujaris.show', $pujari)->with('success', 'Pujari status updated.');
    }

    public function destroy(PujariProfile $pujari)
    {
        DB::transaction(function () use ($pujari) {
            $pujari->delete();
            if ($pujari->user) {
                $pujari->user->update(['status' => 'inactive']);
            }
        });

        return redirect()->route('admin.verifications.pujaris.index')->with('success', 'Priest removed successfully.');
    }
}