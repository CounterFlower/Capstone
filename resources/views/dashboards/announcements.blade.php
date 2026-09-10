<section class="tab-panel {{ ($activeTab ?? '') === 'events' ? 'active' : '' }}" data-tab-panel="events">
    @if (session('status'))
        <div style="padding: 12px 16px; background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; border-radius: 8px; margin-bottom: 20px;">
            {{ session('status') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 24px; align-items: start;">
        
        <!-- Scheduled Activities Table -->
        <article class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                    <h2 style="margin: 0; font-size: 1.35rem;">Scheduled Activities</h2>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 4px;">Live overview of community assemblies, missions, and registrations.</p>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                            <th style="padding: 10px 8px;">Event ID</th>
                            <th style="padding: 10px 8px;">Event Name</th>
                            <th style="padding: 10px 8px;">Start Time</th>
                            <th style="padding: 10px 8px;">End Time</th>
                            <th style="padding: 10px 8px;">Venue</th>
                            <th style="padding: 10px 8px; text-align: center;">Slots Filled</th>
                            <th style="padding: 10px 8px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                            @php
                                $registered = $event->registered_count ?? 0;
                                $capacity = $event->Available_Slots ?? 0;
                                $isFull = $capacity > 0 && $registered >= $capacity;
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; font-weight: 700; color: #1e293b;">#EV-{{ str_pad($event->Event_ID, 3, '0', STR_PAD_LEFT) }}</td>
                                <td style="padding: 12px 8px; font-weight: 600; color: #0f172a;">{{ $event->Event_Name }}</td>
                                <td style="padding: 12px 8px; color: #475569; white-space: nowrap;">
                                    {{ $event->Event_Date ? \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                                <td style="padding: 12px 8px; color: #475569; white-space: nowrap;">
                                    {{ $event->End_Date ? \Carbon\Carbon::parse($event->End_Date)->format('M d, Y h:i A') : '—' }}
                                </td>
                                <td style="padding: 12px 8px; color: #475569;">{{ $event->Location }}</td>
                                <td style="padding: 12px 8px; text-align: center;">
                                    <!-- Dynamic 1/50 Capacity Counter -->
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: {{ $isFull ? '#fee2e2' : '#dcfce7' }}; color: {{ $isFull ? '#991b1b' : '#166534' }};">
                                        {{ $registered }} / {{ $capacity }}
                                    </span>
                                </td>
                                <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.events.edit', $event->Event_ID) }}" style="padding: 5px 10px; background: #3b82f6; color: #ffffff; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600;">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event->Event_ID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 5px 10px; background: #ef4444; color: #ffffff; border: 0; border-radius: 6px; font-size: 0.75rem; cursor: pointer; font-weight: 600;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 24px; text-align: center; color: #94a3b8;">No scheduled events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <!-- New Event Form -->
        <article class="card">
            <h2 style="margin: 0 0 4px 0; font-size: 1.35rem;">Schedule New Activity</h2>
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 18px;">Publish an event with date duration, cover photo, and attendance cap.</p>

            <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" style="display: grid; gap: 14px;">
                @csrf

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Event Title *</label>
                    <input type="text" name="event_name" value="{{ old('event_name') }}" required placeholder="e.g. Free Rabies Vaccination" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Start Time *</label>
                        <input type="datetime-local" name="event_date" value="{{ old('event_date') }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">End Time</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Venue / Location *</label>
                        <input type="text" name="location" value="{{ old('location') }}" required placeholder="e.g. Covered Court" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Capacity (Slots) *</label>
                        <input type="number" name="available_slots" value="{{ old('available_slots', 50) }}" min="1" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*" style="width: 100%; border: 1px dashed #94a3b8; border-radius: 8px; padding: 10px; font-size: 0.82rem; background: #f8fafc;">
                    <small style="color: #64748b; font-size: 0.72rem;">Accepts JPG, PNG, WEBP up to 2MB.</small>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px;">Summary / Description</label>
                    <textarea name="summary" rows="3" placeholder="Brief details about the event..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 12px; font-size: 0.88rem; resize: vertical;">{{ old('summary') }}</textarea>
                </div>

                <button type="submit" style="background: #16a34a; color: #ffffff; border: 0; padding: 11px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; margin-top: 6px;">
                    Publish Event
                </button>
            </form>
        </article>
    </div>
</section>