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
        $categories = DB::table('incident_types')->orderBy('Category')->get();

        return view('public.incident', [
            'categories' => $categories,
        ]);
    }

    public function submitIncidentReport(Request $request)
    {
        $payload = $request->validate([
            'reporter_type' => ['required', 'in:resident,guest'],
            'first_name' => ['required_if:reporter_type,resident', 'nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required_if:reporter_type,resident', 'nullable', 'string', 'max:100'],
            'date_of_birth' => ['required_if:reporter_type,resident', 'nullable', 'date'],
            'guest_first_name' => ['required_if:reporter_type,guest', 'nullable', 'string', 'max:100'],
            'guest_middle_name' => ['nullable', 'string', 'max:100'],
            'guest_last_name' => ['required_if:reporter_type,guest', 'nullable', 'string', 'max:100'],
            'guest_address' => ['required_if:reporter_type,guest', 'nullable', 'string', 'max:255'],
            'guest_contact_number' => ['required_if:reporter_type,guest', 'nullable', 'string', 'max:50'],
            'complainant_name' => ['required', 'string', 'max:255'],
            'respondent_first_name' => ['required', 'string', 'max:100'],
            'respondent_middle_name' => ['nullable', 'string', 'max:100'],
            'respondent_last_name' => ['required', 'string', 'max:100'],
            'respondent_date_of_birth' => ['nullable', 'date'],
            'category_id' => ['required', 'integer', 'exists:incident_types,Category_Id'],
            'complaint_details' => ['required', 'string'],
            'requested_relief' => ['nullable', 'string'],
        ]);

        $respondentName = trim(implode(' ', array_filter([
            $payload['respondent_first_name'] ?? null,
            $payload['respondent_middle_name'] ?? null,
            $payload['respondent_last_name'] ?? null,
        ], fn ($part) => $part !== null && $part !== '')));

        $respondent = DB::table('resident')
            ->whereRaw('TRIM(CONCAT(COALESCE(First_Name, ""), " ", COALESCE(Middle_Name, ""), " ", COALESCE(Last_Name, ""))) = ?', [$respondentName])
            ->when(! empty($payload['respondent_date_of_birth']), fn ($query) => $query->whereDate('Date_of_Birth', $payload['respondent_date_of_birth']))
            ->first(['Resident_ID', 'First_Name', 'Middle_Name', 'Last_Name', 'Date_of_Birth']);

        if (! $respondent) {
            return redirect()->route('public.incidents')
                ->withInput()
                ->with('error', 'Respondent details must match an existing resident record.');
        }

        $complainant = DB::table('resident')
            ->where('First_Name', trim((string) ($payload['first_name'] ?? '')))
            ->where('Last_Name', trim((string) ($payload['last_name'] ?? '')))
            ->when(! empty($payload['date_of_birth']), fn ($query) => $query->whereDate('Date_of_Birth', $payload['date_of_birth']))
            ->first(['Resident_ID']);

        $this->residentService->submitIncidentReport([
            'reporter_type' => $payload['reporter_type'],
            'complainant_name' => $payload['complainant_name'],
            'respondent_name' => $respondentName,
            'category_id' => $payload['category_id'],
            'complaint_details' => $payload['complaint_details'],
            'requested_relief' => $payload['requested_relief'] ?? null,
            'complainant_first_name' => $payload['reporter_type'] === 'guest'
                ? ($payload['guest_first_name'] ?? null)
                : ($payload['first_name'] ?? null),
            'complainant_middle_name' => $payload['reporter_type'] === 'guest'
                ? ($payload['guest_middle_name'] ?? null)
                : ($payload['middle_name'] ?? null),
            'complainant_last_name' => $payload['reporter_type'] === 'guest'
                ? ($payload['guest_last_name'] ?? null)
                : ($payload['last_name'] ?? null),
            'date_of_birth' => $payload['date_of_birth'] ?? null,
            'guest_address' => $payload['guest_address'] ?? null,
            'guest_contact_number' => $payload['guest_contact_number'] ?? null,
            'complainant_resident_id' => $complainant?->Resident_ID,
            'respondent_resident_id' => $respondent->Resident_ID,
        ]);

        return redirect()->route('public.incidents')->with('status', 'Incident report submitted successfully.');
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
