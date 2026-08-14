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
                <p class="section-copy" style="margin-bottom: 16px;">This prototype writes registrations to local storage only. No database tables are used.</p>

                <form method="POST" action="{{ route('public.events.submit') }}" style="display: grid; gap: 14px;">
                    @csrf

                    <label>
                        <strong>Resident Name</strong>
                        <input type="text" name="resident_name" value="{{ old('resident_name') }}" required style="width: 100%; margin-top: 8px; padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                    </label>

                    <label>
                        <strong>Contact Number</strong>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" required style="width: 100%; margin-top: 8px; padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                    </label>

                    <label>
                        <strong>Purok</strong>
                        <input type="text" name="purok" value="{{ old('purok') }}" required style="width: 100%; margin-top: 8px; padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit;">
                    </label>

                    <label>
                        <strong>Select Event</strong>
                        <select name="event_id" required style="width: 100%; margin-top: 8px; padding: 14px 16px; border-radius: 14px; border: 1px solid rgba(22, 48, 36, 0.12); font: inherit; background: #fff;">
                            <option value="">Choose an event</option>
                            @foreach ($events as $event)
                                <option value="{{ $event['id'] }}" {{ old('event_id', $selectedEvent) === $event['id'] ? 'selected' : '' }}>
                                    {{ $event['title'] }} - {{ $event['date'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    @if ($errors->any())
                        <div class="list-item" style="color: #b55343;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <button type="submit" class="button primary" style="border: 0; cursor: pointer;">Submit Registration</button>
                    </div>
                </form>
            </article>

            <article class="card">
                <h3>Available activities</h3>
                @foreach ($events as $event)
                    <div class="list-item">
                        <strong>{{ $event['title'] }}</strong>
                        {{ $event['summary'] }}<br>
                        {{ $event['date'] }} at {{ $event['time'] }}<br>
                        {{ $event['venue'] }}
                    </div>
                @endforeach
            </article>
        </div>
    </section>
@endsection
