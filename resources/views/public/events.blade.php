@extends('layouts.public')

@section('content')
    <section class="section">
        <div class="section-head">
            <div>
                <p class="eyebrow">Public Service Module</p>
                <h2>Barangay Event Registration</h2>
            </div>
            <a class="button secondary" href="{{ route('home') }}">Back to Home</a>
        </div>

        @if (session('status'))
            <article class="card" style="margin-bottom: 18px;">
                <p class="eyebrow">Submission Status</p>
                <h3>Registration saved</h3>
                <p>{{ session('status') }}</p>
            </article>
        @endif

        <div class="content-grid">
            <article class="card">
                <h3>Resident registration form</h3>
                <p class="section-copy" style="margin-bottom: 16px;">Please select an activity first to begin the registration process.</p>

                <form method="POST" action="{{ route('public.events.submit') }}" style="display: grid; gap: 14px;">
                    @csrf

                    <!-- Step 1: Event Selection -->
                    <label>
                        <strong>Select Event <span style="color: #b55343;">*</span></strong>
                        <select id="event-selector" name="event_id" required style="width: 100%; margin-top: 8px; padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(22, 48, 36, 0.18); font: inherit; background: #fff; font-weight: 600;">
                            <option value="">-- Choose an upcoming activity --</option>
                            @foreach ($events as $event)
                                @php
                                    $eId = is_array($event) ? $event['id'] : ($event->Event_ID ?? $event->id);
                                    $eTitle = is_array($event) ? $event['title'] : ($event->Event_Name ?? $event->title);
                                    $eDate = is_array($event) ? $event['date'] : ($event->Event_Date ?? $event->date);
                                @endphp
                                <option value="{{ $eId }}" {{ (string)old('event_id', $selectedEvent) === (string)$eId ? 'selected' : '' }}>
                                    {{ $eTitle }} &bull; {{ $eDate }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <!-- Event Summary Card -->
                    <div id="event-highlight-card" style="display: none; padding: 14px 16px; background: rgba(45, 124, 84, 0.08); border-left: 4px solid var(--success); border-radius: 10px; font-size: 0.88rem;">
                        <strong id="highlight-title" style="display: block; font-size: 1rem; color: var(--text);"></strong>
                        <div id="highlight-datetime" style="margin-top: 4px; color: var(--muted);"></div>
                        <div id="highlight-venue" style="color: var(--muted);"></div>
                        <p id="highlight-summary" style="margin-top: 6px; font-style: italic; color: var(--text);"></p>
                    </div>

                    <!-- Step 2: Resident Information Fields -->
                    <div id="registration-details-section" style="display: none; flex-direction: column; gap: 14px;">

                        <!-- 3 Name Fields Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <label>
                                <strong>First Name <span style="color: #b55343;">*</span></strong>
                                <input type="text" id="input-first-name" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Francis Julius" style="width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                            </label>

                            <label>
                                <strong>Middle Name <small style="color: var(--muted); font-weight: 400;">(Optional)</small></strong>
                                <input type="text" id="input-middle-name" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Galias" style="width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                            </label>

                            <label>
                                <strong>Last Name <span style="color: #b55343;">*</span></strong>
                                <input type="text" id="input-last-name" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Castuera" style="width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                            </label>
                        </div>

                        <!-- Contact & Purok -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <label>
                                <strong>Contact Number</strong>
                                <input type="text" id="input-contact-number" name="contact_number" value="{{ old('contact_number') }}" placeholder="e.g. 09093241232" style="width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                            </label>

                            <label>
                                <strong>Purok / Zone</strong>
                                <input type="text" id="input-purok" name="purok" value="{{ old('purok') }}" placeholder="e.g. Purok 6" style="width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                            </label>
                        </div>

                        <div>
                            <button type="submit" class="button primary" style="border: 0; cursor: pointer; margin-top: 6px;">Submit Registration</button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="list-item" style="color: #b55343; border-left: 3px solid #b55343; padding-left: 10px; margin-top: 10px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </form>
            </article>

            <!-- Activity List Reference Card -->
            <article class="card">
                <h3>Available activities</h3>
                @foreach ($events as $event)
                    @php
                        $cardTitle = is_array($event) ? $event['title'] : ($event->Event_Name ?? $event->title);
                        $cardSummary = is_array($event) ? $event['summary'] : ($event->Summary ?? $event->summary ?? '');
                        $cardDate = is_array($event) ? $event['date'] : ($event->Event_Date ?? $event->date);
                        $cardTime = is_array($event) ? ($event['time'] ?? '') : ($event->Start_Time ?? $event->time ?? '');
                        $cardVenue = is_array($event) ? $event['venue'] : ($event->Location ?? $event->venue ?? 'TBA');
                    @endphp
                    <div class="list-item">
                        <strong>{{ $cardTitle }}</strong>
                        @if ($cardSummary)
                            <p style="margin: 4px 0 6px 0;">{{ $cardSummary }}</p>
                        @endif
                        <span style="font-size: 0.85rem; color: var(--muted);">
                            {{ $cardDate }} {{ $cardTime ? 'at '.$cardTime : '' }}<br>
                            Venue: {{ $cardVenue }}
                        </span>
                    </div>
                @endforeach
            </article>
        </div>
    </section>

    <!-- UI State Synchronization Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const eventsData = @json($events);

        const selector = document.getElementById('event-selector');
        const formSection = document.getElementById('registration-details-section');
        const highlightBox = document.getElementById('event-highlight-card');
        const hlTitle = document.getElementById('highlight-title');
        const hlDatetime = document.getElementById('highlight-datetime');
        const hlVenue = document.getElementById('highlight-venue');
        const hlSummary = document.getElementById('highlight-summary');

        const firstNameInput = document.getElementById('input-first-name');
        const lastNameInput = document.getElementById('input-last-name');

        function handleEventChange() {
            const selectedId = selector.value;

            if (!selectedId) {
                formSection.style.display = 'none';
                highlightBox.style.display = 'none';
                firstNameInput.removeAttribute('required');
                lastNameInput.removeAttribute('required');
                return;
            }

            const found = eventsData.find(e => {
                const id = e.id !== undefined ? e.id : (e.Event_ID !== undefined ? e.Event_ID : e.id);
                return String(id) === String(selectedId);
            });

            if (found) {
                hlTitle.textContent = found.title || found.Event_Name || 'Selected Event';
                const dateText = found.date || found.Event_Date || '';
                const timeText = found.time || found.Start_Time || '';
                hlDatetime.textContent = dateText + (timeText ? ' at ' + timeText : '');
                hlVenue.textContent = 'Venue: ' + (found.venue || found.Location || 'TBA');
                hlSummary.textContent = found.summary || found.Summary || '';
                highlightBox.style.display = 'block';
            } else {
                highlightBox.style.display = 'none';
            }

            formSection.style.display = 'flex';
            firstNameInput.setAttribute('required', 'required');
            lastNameInput.setAttribute('required', 'required');
        }

        selector.addEventListener('change', handleEventChange);
        handleEventChange();
    });
    </script>
@endsection