<?php

namespace App\Http\Controllers;

use App\Services\PrototypeEventService;
use App\Services\ResidentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicPageController extends Controller
{
    public function __construct(
        protected PrototypeEventService $prototypeEventService,
        protected ResidentService $residentService,
    ) {}

    public function home()
    {
        return view('public.home', [
            'events' => $this->prototypeEventService->events(),
            'photos' => $this->prototypeEventService->photoGallery(),
        ]);
    }

    public function incident()
    {
        return view('public.incident');
    }

    public function documents()
    {
        return view('public.documents');
    }

    public function submitDocumentRequest(Request $request)
    {
        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['nullable', 'string', 'max:50'],
            'purok' => ['nullable', 'string', 'max:100'],
            'document_type' => ['required', 'string', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'years_stayed' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $validDocumentTypes = [
            'Barangay Clearance',
            'Certificate of Residency',
            'Certificate of Indigency',
            'Business Endorsement',
        ];

        if (! in_array($payload['document_type'], $validDocumentTypes, true)) {
            return back()->withErrors(['document_type' => 'Selected document type is not available.'])->withInput();
        }

        try {
            $result = $this->residentService->submitDocumentRequest($payload);

            return redirect()->route('public.documents')->with('status', 'Document request submitted successfully for '.$result->resident_name.'.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors([
                'resident_match' => $exception->getMessage(),
            ])->withInput();
        }
    }

    public function events(Request $request)
    {
        return view('public.events', [
            'events' => $this->prototypeEventService->events(),
            'selectedEvent' => $request->query('event'),
        ]);
    }

    public function submitEventRegistration(Request $request)
    {
        $payload = $request->validate([
            'resident_name' => ['required', 'string', 'max:120'],
            'contact_number' => ['required', 'string', 'max:30'],
            'purok' => ['required', 'string', 'max:60'],
            'event_id' => ['required', 'string'],
        ]);

        try {
            $event = $this->prototypeEventService->registerForEvent($payload);

            return redirect()
                ->route('public.events', ['event' => $event['id']])
                ->with('status', 'Registration submitted for '.$event['title'].'.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['event_id' => $exception->getMessage()])->withInput();
        }
    }
}
