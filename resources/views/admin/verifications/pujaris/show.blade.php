@extends('admin.layouts.app')
@section('title', 'Pujari Dossier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.verifications.pujaris.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Verification List</a>
        <h4 class="fw-bold mb-0">Dossier: {{ $pujari->user->name ?? 'Pujari #'.$pujari->id }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($pujari->verification_status) }}</span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Profile & Languages -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Background & Skills</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Experience</label>
                        <div class="fw-bold">{{ $pujari->experience_years ?? 0 }} Years</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Temple Affiliation</label>
                        <div class="fw-bold">{{ $pujari->templeAssignments->first()->temple->name ?? 'Independent Freelance' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Spoken Languages & Ritual Dialects</label>
                        <div>
                            @forelse($pujari->languages as $lang)
                                <span class="badge bg-light text-dark border me-1">{{ $lang->language_name ?? $lang->name }}</span>
                            @empty
                                <span class="text-muted small">No languages indicated.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents / ID Proofs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Uploaded Verification Documents</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($pujari->documents as $doc)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <div class="fw-bold">{{ $doc->document_type ?? 'Government ID' }}</div>
                                <small class="text-muted">{{ $doc->document_number ?? 'Doc #'.$doc->id }}</small>
                            </div>
                            <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Document
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No digital documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Decision Form -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Verification Decision</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.verifications.pujaris.update-status', $pujari) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Decision</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'verified', 'rejected', 'suspended'] as $st)
                                <option value="{{ $st }}" {{ $pujari->verification_status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Audit Remarks / Justification</label>
                        <textarea name="remarks" rows="4" class="form-control" placeholder="E.g., Government documents and certificates verified over phone interview." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Submit Decision</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection