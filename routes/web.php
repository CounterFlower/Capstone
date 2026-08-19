<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ResidentController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

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

Route::post('/admin/residents', [ResidentController::class, 'store'])->name('admin.residents.store');
Route::post('/admin/residents/assign-household', [ResidentController::class, 'assignHousehold'])->name('admin.residents.assign-household');
Route::post('/admin/residents/update', [ResidentController::class, 'update'])->name('admin.residents.update');
Route::post('/admin/residents/verify', [ResidentController::class, 'verify'])->name('admin.residents.verify');

Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
