<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\QueryException;

if (! function_exists('prototypeEvents')) {
    function prototypeEvents(): array
    {
        return [
            [
                'id' => 'barangay-assembly',
                'title' => 'Barangay Assembly',
                'date' => 'May 16, 2026',
                'time' => '2:00 PM',
                'venue' => 'Covered Court',
                'summary' => 'Monthly assembly for community concerns, reports, and announcements.',
            ],
            [
                'id' => 'medical-mission',
                'title' => 'Medical Mission',
                'date' => 'May 18, 2026',
                'time' => '8:00 AM',
                'venue' => 'Barangay Hall',
                'summary' => 'Free consultation, blood pressure check, and basic health screening.',
            ],
            [
                'id' => 'clean-up-drive',
                'title' => 'Clean-Up Drive',
                'date' => 'May 20, 2026',
                'time' => '7:00 AM',
                'venue' => 'Purok 3 and Purok 4',
                'summary' => 'Community clean-up and drainage clearing activity.',
            ],
        ];
    }
}

if (! function_exists('prototypeRegistrationPath')) {
    function prototypeRegistrationPath(): string
    {
        return storage_path('app/prototype_event_registrations.json');
    }
}

if (! function_exists('prototypeReadRegistrations')) {
    function prototypeReadRegistrations(): array
    {
        $path = prototypeRegistrationPath();

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode(File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }
}

if (! function_exists('prototypeWriteRegistrations')) {
    function prototypeWriteRegistrations(array $registrations): void
    {
        File::put(prototypeRegistrationPath(), json_encode($registrations, JSON_PRETTY_PRINT));
    }
}

if (! function_exists('prototypePhotoGallery')) {
    function prototypePhotoGallery(): array
    {
        $files = collect(File::files(resource_path('photos')))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();

        return $files->map(function ($file, int $index) {
            return [
                'file' => $file->getFilename(),
                'title' => 'EVENT NAME',
                'description' => 'description of the event',
                'key' => 'photo-'.$index,
            ];
        })->all();
    }
}

Route::get('/photos/{filename}', function (string $filename) {
    $safeFilename = basename($filename);
    $path = resource_path('photos/'.$safeFilename);

    abort_unless(File::exists($path), 404);

    return response()->file($path, [
        'Content-Type' => File::mimeType($path),
    ]);
})->where('filename', '.*')->name('public.photos');

Route::get('/', function () {
    return view('public.home', [
        'events' => prototypeEvents(),
        'photos' => prototypePhotoGallery(),
    ]);
})->name('home');

Route::get('/incident-reporting', function () {
    return view('public.incident');
})->name('public.incidents');

Route::get('/document-requests', function () {
    return view('public.documents');
})->name('public.documents');

Route::get('/event-registration', function (Request $request) {
    return view('public.events', [
        'events' => prototypeEvents(),
        'selectedEvent' => $request->query('event'),
    ]);
})->name('public.events');

Route::post('/event-registration', function (Request $request) {
    $payload = $request->validate([
        'resident_name' => ['required', 'string', 'max:120'],
        'contact_number' => ['required', 'string', 'max:30'],
        'purok' => ['required', 'string', 'max:60'],
        'event_id' => ['required', 'string'],
    ]);

    $events = collect(prototypeEvents())->keyBy('id');
    $event = $events->get($payload['event_id']);

    if (! $event) {
        return back()->withErrors(['event_id' => 'Selected event is not available.'])->withInput();
    }

    $registrations = prototypeReadRegistrations();
    $registrations[] = [
        'reference' => 'EVT-'.str_pad((string) (count($registrations) + 1), 3, '0', STR_PAD_LEFT),
        'resident_name' => $payload['resident_name'],
        'contact_number' => $payload['contact_number'],
        'purok' => $payload['purok'],
        'event_id' => $event['id'],
        'event_title' => $event['title'],
        'event_date' => $event['date'],
        'event_time' => $event['time'],
        'submitted_at' => now()->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
    ];

    prototypeWriteRegistrations($registrations);

    return redirect()
        ->route('public.events', ['event' => $event['id']])
        ->with('status', 'Registration submitted for '.$event['title'].'.');
})->name('public.events.submit');

Route::get('/admin/login', function () {
    if (session('is_admin')) {
        return redirect()->route('admin.dashboard');
    }

    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin') {
        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->route('admin.dashboard');
    }

    return back()
        ->withErrors(['login' => 'Invalid credentials. Use admin / admin for this prototype.'])
        ->onlyInput('username');
})->name('admin.login.submit');

Route::post('/admin/logout', function (Request $request) {
    $request->session()->forget('is_admin');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
})->name('admin.logout');

Route::post('/admin/residents', function (Request $request) {
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

    $payload = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'date_of_birth' => ['nullable', 'date'],
        'gender' => ['nullable', 'string', 'max:20'],
        'contact_number' => ['nullable', 'string', 'max:50'],
        'house_number' => ['required', 'string', 'max:50'],
        'zone_purok' => ['required', 'string', 'max:100'],
        'household_id' => ['nullable', 'integer'],
    ]);

    try {
        DB::transaction(function () use ($payload) {
            $householdIndex = null;

            $household = DB::table('household')
                ->where('House_Number', $payload['house_number'])
                ->where('Zone_Purok', $payload['zone_purok'])
                ->first();

            if ($payload['household_id'] !== null) {
                $officialHousehold = DB::table('household')
                    ->where('Household_Id', $payload['household_id'])
                    ->first();

                if ($officialHousehold) {
                    $householdIndex = $officialHousehold->Household_Index;
                } elseif ($household) {
                    DB::table('household')
                        ->where('Household_Index', $household->Household_Index)
                        ->update(['Household_Id' => $payload['household_id']]);

                    $householdIndex = $household->Household_Index;
                } else {
                    $householdIndex = DB::table('household')->insertGetId([
                        'Household_Id' => $payload['household_id'],
                        'House_Number' => $payload['house_number'],
                        'Zone_Purok' => $payload['zone_purok'],
                    ]);
                }
            } elseif ($household) {
                $householdIndex = $household->Household_Index;
            } else {
                $householdIndex = DB::table('household')->insertGetId([
                    'Household_Id' => null,
                    'House_Number' => $payload['house_number'],
                    'Zone_Purok' => $payload['zone_purok'],
                ]);
            }

            DB::table('resident')->insert([
                'First_Name' => $payload['first_name'],
                'Middle_Name' => $payload['middle_name'] ?? null,
                'Last_Name' => $payload['last_name'],
                'Date_of_Birth' => $payload['date_of_birth'] ?? null,
                'Gender' => $payload['gender'] ?? null,
                'Contact_Number' => $payload['contact_number'] ?? null,
                'Household_ID' => $householdIndex,
                'Is_Verified' => 0,
            ]);
        });
    } catch (RuntimeException $exception) {
        return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])->withErrors([
            'household' => $exception->getMessage(),
        ])->withInput();
    } catch (QueryException $exception) {
        return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])->withErrors([
            'resident' => 'The resident could not be added. Check the supplied database values.',
        ])->withInput();
    }

    return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])
        ->with('status', 'Resident profile added successfully.');
})->name('admin.residents.store');

Route::post('/admin/residents/assign-household', function (Request $request) {
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

    $payload = $request->validate([
        'resident_id' => ['required', 'integer'],
        'household_id' => ['required', 'integer'],
        'house_number' => ['required', 'string', 'max:50'],
        'zone_purok' => ['required', 'string', 'max:100'],
    ]);

    try {
        DB::transaction(function () use ($payload) {
            $resident = DB::table('resident')
                ->where('Resident_ID', $payload['resident_id'])
                ->first(['Resident_ID', 'Household_ID']);
            $householdIndex = DB::table('household')
                ->where('Household_Id', $payload['household_id'])
                ->value('Household_Index');

            if (! $resident) {
                throw new RuntimeException('No resident matches the supplied resident ID.');
            }

            if ($householdIndex === null) {
                if (! $resident->Household_ID) {
                    throw new RuntimeException('The resident is not assigned to a household row.');
                }

                DB::table('household')
                    ->where('Household_Index', $resident->Household_ID)
                    ->update([
                        'Household_Id' => $payload['household_id'],
                        'House_Number' => $payload['house_number'],
                        'Zone_Purok' => $payload['zone_purok'],
                    ]);

                return;
            }

            DB::table('household')
                ->where('Household_Index', $householdIndex)
                ->update([
                    'House_Number' => $payload['house_number'],
                    'Zone_Purok' => $payload['zone_purok'],
                ]);

            DB::table('resident')
                ->where('Resident_ID', $resident->Resident_ID)
                ->update(['Household_ID' => $householdIndex]);

            if ($resident->Household_ID && $resident->Household_ID !== $householdIndex) {
                $stillAssigned = DB::table('resident')
                    ->where('Household_ID', $resident->Household_ID)
                    ->exists();

                if (! $stillAssigned) {
                    DB::table('household')
                        ->where('Household_Index', $resident->Household_ID)
                        ->delete();
                }
            }
        });
    } catch (RuntimeException $exception) {
        return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])->withErrors([
            'assignment' => $exception->getMessage(),
        ])->withInput();
    } catch (QueryException $exception) {
        return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])->withErrors([
            'assignment' => 'The household assignment could not be updated.',
        ])->withInput();
    }

    return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])
        ->with('status', 'Resident household assignment updated.');
})->name('admin.residents.assign-household');

Route::post('/admin/residents/update', function (Request $request) {
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

    $payload = $request->validate([
        'resident_id' => ['required', 'integer'],
        'first_name' => ['required', 'string', 'max:255'],
        'middle_name' => ['nullable', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'date_of_birth' => ['nullable', 'date'],
        'gender' => ['nullable', 'string', 'max:20'],
        'contact_number' => ['nullable', 'string', 'max:50'],
    ]);

    $residentExists = DB::table('resident')
        ->where('Resident_ID', $payload['resident_id'])
        ->exists();

    if (! $residentExists) {
        return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])
            ->withErrors(['details' => 'No resident matches the supplied resident ID.'])
            ->withInput();
    }

    DB::table('resident')
        ->where('Resident_ID', $payload['resident_id'])
        ->update([
            'First_Name' => $payload['first_name'],
            'Middle_Name' => $payload['middle_name'] ?? null,
            'Last_Name' => $payload['last_name'],
            'Date_of_Birth' => $payload['date_of_birth'] ?? null,
            'Gender' => $payload['gender'] ?? null,
            'Contact_Number' => $payload['contact_number'] ?? null,
        ]);

    return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])
        ->with('status', 'Resident profile details updated.');
})->name('admin.residents.update');

Route::post('/admin/residents/verify', function (Request $request) {
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

    $payload = $request->validate([
        'resident_id' => ['required', 'integer'],
        'is_verified' => ['required', 'boolean'],
    ]);

    DB::table('resident')
        ->where('Resident_ID', $payload['resident_id'])
        ->update(['Is_Verified' => $payload['is_verified']]);

    return redirect()->route('admin.dashboard', ['tab' => $request->input('active_tab', 'residents')])
        ->with('status', $payload['is_verified']
        ? 'Resident profile marked as verified.'
        : 'Resident profile returned to pending.');
})->name('admin.residents.verify');

Route::get('/admin', function (Request $request) {
    if (! session('is_admin')) {
        return redirect()->route('admin.login');
    }

    $events = prototypeEvents();
    $registrations = prototypeReadRegistrations();
    $residentProfiles = DB::table('resident')
        ->leftJoin('household', 'resident.Household_ID', '=', 'household.Household_Index')
        ->select([
            'resident.Resident_ID',
            'resident.First_Name',
            'resident.Middle_Name',
            'resident.Last_Name',
            'resident.Household_ID as Household_Index',
            'household.Household_Id',
            'household.House_Number',
            'resident.Date_of_Birth',
            'resident.Gender',
            'resident.Contact_Number',
            'resident.Is_Verified',
            'household.Zone_Purok',
        ])
        ->orderBy('resident.Last_Name')
        ->get();

    $eventCounts = collect($events)->map(function (array $event) use ($registrations) {
        $count = collect($registrations)
            ->where('event_id', $event['id'])
            ->count();

        return [
            'title' => $event['title'],
            'date' => $event['date'],
            'count' => $count,
        ];
    })->all();

    return view('admin.dashboard', [
        'registrations' => $registrations,
        'eventCounts' => $eventCounts,
        'residentProfiles' => $residentProfiles,
        'activeTab' => $request->query('tab', 'overview'),
    ]);
})->name('admin.dashboard');
