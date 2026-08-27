<?php

namespace App\Http\Controllers;

use App\Services\ResidentService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use RuntimeException;

class ResidentController extends Controller
{
    public function __construct(protected ResidentService $residentService) {}

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
            'household_id' => ['nullable', 'integer'],
        ]);

        try {
            $this->residentService->createResident($payload);
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'household', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'resident', 'The resident could not be added. Check the supplied database values.');
        }

        return $this->redirectToResidents($request, 'Resident profile added successfully.');
    }

    public function assignHousehold(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'household_id' => ['required', 'integer'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
        ]);

        try {
            $this->residentService->assignHousehold($payload);
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'assignment', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'assignment', 'The household assignment could not be updated.');
        }

        return $this->redirectToResidents($request, 'Resident household assignment updated.');
    }

    public function update(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['required', 'string', 'max:50'],
            'zone_purok' => ['required', 'string', 'max:100'],
            'household_id' => ['nullable', 'integer'],
        ]);

        try {
            $this->residentService->updateResident($payload);
        } catch (RuntimeException $exception) {
            return $this->redirectWithErrors($request, 'details', $exception->getMessage());
        } catch (QueryException $exception) {
            return $this->redirectWithErrors($request, 'details', 'The resident profile could not be updated.');
        }

        return $this->redirectToResidents($request, 'Resident profile updated successfully.');
    }

    public function verify(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'resident_id' => ['required', 'integer'],
            'is_verified' => ['required', 'boolean'],
        ]);

        $this->residentService->verifyResident((int) $payload['resident_id'], (bool) $payload['is_verified']);

        return $this->redirectToResidents($request, $payload['is_verified']
            ? 'Resident profile marked as verified.'
            : 'Resident profile returned to pending.');
    }

    public function approveDocumentRequest(Request $request)
    {
        $this->ensureAdmin();

        $payload = $request->validate([
            'request_id' => ['required', 'integer'],
        ]);

        $updated = $this->residentService->approveDocumentRequest((int) $payload['request_id']);

        if (! $updated) {
            return redirect()->route('admin.dashboard', ['tab' => 'requests'])
                ->withErrors(['document_request' => 'The selected request could not be approved.']);
        }

        return redirect()->route('admin.dashboard', ['tab' => 'requests'])
            ->with('status', 'Document request approved successfully.');
    }

    private function ensureAdmin(): void
    {
        if (! session('is_admin')) {
            abort(redirect()->route('admin.login'));
        }
    }

    private function redirectToResidents(Request $request, string $status)
    {
        return redirect()->route('admin.dashboard', [
            'tab' => $request->input('active_tab', 'residents'),
        ])->with('status', $status);
    }

    private function redirectWithErrors(Request $request, string $key, string $message)
    {
        return redirect()->route('admin.dashboard', [
            'tab' => $request->input('active_tab', 'residents'),
        ])->withErrors([$key => $message])->withInput();
    }
}
