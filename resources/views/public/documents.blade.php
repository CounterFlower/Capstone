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

        <!-- Document Type Selector Cards -->
        <div class="service-grid" id="document-selector-grid" style="cursor: pointer;">
            <article class="service-card doc-tab-btn" 
                     data-doctype="Barangay Clearance" 
                     style="transition: all 0.2s ease; border: 2px solid transparent; user-select: none;">
                <h3>Barangay Clearance</h3>
                <p>General-purpose certification for local records, employment, and legal transactions.</p>
                <span class="badge-select" style="display: inline-block; margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: #166534; background: #dcfce7; padding: 4px 10px; border-radius: 999px;">Click to Select &darr;</span>
            </article>

            <article class="service-card doc-tab-btn" 
                     data-doctype="Certificate of Residency" 
                     style="transition: all 0.2s ease; border: 2px solid transparent; user-select: none;">
                <h3>Certificate of Residency</h3>
                <p>Proof of bona fide residency within Barangay Bagumbayan for utility or bank requirements.</p>
                <span class="badge-select" style="display: inline-block; margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: #166534; background: #dcfce7; padding: 4px 10px; border-radius: 999px;">Click to Select &darr;</span>
            </article>

            <article class="service-card doc-tab-btn" 
                     data-doctype="Business Endorsement" 
                     style="transition: all 0.2s ease; border: 2px solid transparent; user-select: none;">
                <h3>Business Endorsement</h3>
                <p>Barangay endorsement and clearances for micro-enterprises, sari-sari stores, and permits.</p>
                <span class="badge-select" style="display: inline-block; margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: #166534; background: #dcfce7; padding: 4px 10px; border-radius: 999px;">Click to Select &darr;</span>
            </article>
        </div>

        <!-- Form Section -->
        <section class="section" id="request-slip-section" style="margin-top: 24px;">
            <div class="content-grid request-layout">
                <article class="card form-card">
                    <div class="card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 20px;">
                        <div>
                            <p class="eyebrow compact">Official Request Slip</p>
                            <h3 id="dynamic-doc-title" style="margin: 4px 0 0; color: #15803d; font-size: 1.35rem;">
                                Requesting: Barangay Clearance
                            </h3>
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

                        <!-- Hidden Document Type Field Populated by the Selected Tab -->
                        <input type="hidden" name="document_type" id="selected_document_type" value="{{ old('document_type', 'Barangay Clearance') }}" required>

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
                                <span>Years Stayed in Barangay</span>
                                <input type="number" name="years_stayed" min="0" max="100" value="{{ old('years_stayed') }}" placeholder="5">
                            </label>
                            <label>
                                <span>Specific Purpose</span>
                                <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. Job requirement / scholarship / banking" required>
                            </label>
                        </div>

                        <div class="submit-row" style="margin-top: 18px;">
                            <button type="submit" class="button primary" id="submit-doc-btn">
                                Submit Barangay Clearance Request
                            </button>
                        </div>
                    </form>
                </article>

                <aside class="card side-card">
                    <h3>Processing flow</h3>
                    <div class="process-list">
                        <div class="process-step"><strong>1.</strong> Choose document type above.</div>
                        <div class="process-step"><strong>2.</strong> Complete your resident information.</div>
                        <div class="process-step"><strong>3.</strong> Submit for barangay admin review.</div>
                        <div class="process-step"><strong>4.</strong> Collect your document upon release.</div>
                    </div>

                    <div class="info-box" style="margin-top: 20px;">
                        <p class="eyebrow compact">Requirements</p>
                        <ul>
                            <li>Registered resident of Barangay Bagumbayan</li>
                            <li>Clear and accurate purpose</li>
                            <li>Personal pickup with valid ID required</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </section>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.doc-tab-btn');
            const hiddenInput = document.getElementById('selected_document_type');
            const dynamicTitle = document.getElementById('dynamic-doc-title');
            const submitBtn = document.getElementById('submit-doc-btn');
            const formSection = document.getElementById('request-slip-section');

            function setActiveDocument(docType, shouldScroll = false) {
                hiddenInput.value = docType;
                dynamicTitle.textContent = 'Requesting: ' + docType;
                submitBtn.textContent = 'Submit ' + docType + ' Request';

                tabs.forEach(tab => {
                    const isCurrent = tab.getAttribute('data-doctype') === docType;
                    const badge = tab.querySelector('.badge-select');
                    if (isCurrent) {
                        tab.style.borderColor = '#16a34a';
                        tab.style.boxShadow = '0 0 0 2px rgba(22, 163, 74, 0.25)';
                        tab.style.backgroundColor = '#f0fdf4';
                        if (badge) {
                            badge.textContent = '✓ Currently Selected';
                            badge.style.background = '#16a34a';
                            badge.style.color = '#ffffff';
                        }
                    } else {
                        tab.style.borderColor = '#e2e8f0';
                        tab.style.boxShadow = 'none';
                        tab.style.backgroundColor = '#ffffff';
                        if (badge) {
                            badge.textContent = 'Click to Select';
                            badge.style.background = '#f1f5f9';
                            badge.style.color = '#475569';
                        }
                    }
                });

                if (shouldScroll && formSection) {
                    formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            // Bind click handlers to cards
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const selectedType = this.getAttribute('data-doctype');
                    setActiveDocument(selectedType, true);
                });
            });

            // Initialize selection (respects old input on validation errors)
            setActiveDocument(hiddenInput.value || 'Barangay Clearance', false);
        });
    </script>
@endsection