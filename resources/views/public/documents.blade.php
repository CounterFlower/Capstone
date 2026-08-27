@extends('layouts.public')

@section('content')
    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Public Service Module</p>
                <h2>Document Requests</h2>
            </div>
            <a class="button secondary" href="{{ route('home') }}">Back to Home</a>
        </div>

        <div class="service-grid">
            <article class="service-card">
                <h3>Barangay Clearance</h3>
                <p>General-purpose certification for local records and transactions.</p>
            </article>
            <article class="service-card">
                <h3>Certificate of Residency</h3>
                <p>Proof of current residence within Barangay Bagumbayan.</p>
            </article>
            <article class="service-card">
                <h3>Business Endorsement</h3>
                <p>Prototype request type for small business-related endorsements.</p>
            </article>
        </div>

        <section class="section">
            <div class="content-grid request-layout">
                <article class="card form-card">
                    <div class="card-header">
                        <div>
                            <p class="eyebrow compact">Barangay Clearance / Request Form</p>
                            <h3>Resident Request Slip</h3>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="status-box success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="status-box error">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('public.documents.submit') }}" class="request-form">
                        @csrf

                        <div class="form-grid three-col">
                            <label>
                                <span>First Name</span>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required>
                            </label>
                            <label>
                                <span>Middle Name</span>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional">
                            </label>
                            <label>
                                <span>Last Name</span>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
                            </label>
                        </div>

                        <div class="form-grid four-col">
                            <label>
                                <span>Date of Birth</span>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            </label>
                            <label>
                                <span>Contact Number</span>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="0917 123 4567">
                            </label>
                            <label>
                                <span>House Number</span>
                                <input type="text" name="house_number" value="{{ old('house_number') }}" placeholder="14">
                            </label>
                            <label>
                                <span>Purok</span>
                                <input type="text" name="purok" value="{{ old('purok') }}" placeholder="Purok 2">
                            </label>
                        </div>

                        <div class="form-grid two-col">
                            <label>
                                <span>Document Type</span>
                                <select name="document_type" required>
                                    <option value="">Select document</option>
                                    <option value="Barangay Clearance" {{ old('document_type') === 'Barangay Clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                                    <option value="Certificate of Residency" {{ old('document_type') === 'Certificate of Residency' ? 'selected' : '' }}>Certificate of Residency</option>
                                    <option value="Certificate of Indigency" {{ old('document_type') === 'Certificate of Indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
                                    <option value="Business Endorsement" {{ old('document_type') === 'Business Endorsement' ? 'selected' : '' }}>Business Endorsement</option>
                                </select>
                            </label>
                            <label>
                                <span>Years Stayed</span>
                                <input type="number" name="years_stayed" min="0" max="100" value="{{ old('years_stayed') }}" placeholder="5">
                            </label>
                        </div>

                        <div class="form-grid single-col">
                            <label>
                                <span>Purpose</span>
                                <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. School requirement / employment / loan">
                            </label>
                        </div>

                        <div class="submit-row">
                            <button type="submit" class="button primary">Submit Request</button>
                        </div>
                    </form>
                </article>

                <aside class="card side-card">
                    <h3>Processing flow</h3>
                    <div class="process-list">
                        <div class="process-step"><strong>1.</strong> Fill out the request form.</div>
                        <div class="process-step"><strong>2.</strong> Submit and wait for staff review.</div>
                        <div class="process-step"><strong>3.</strong> Verify resident data and purpose.</div>
                        <div class="process-step"><strong>4.</strong> Approve and release the requested document.</div>
                    </div>

                    <div class="info-box">
                        <p class="eyebrow compact">Requirements</p>
                        <ul>
                            <li>Valid resident information</li>
                            <li>Clear purpose of request</li>
                            <li>Supporting documents when needed</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </section>
    </section>
@endsection
