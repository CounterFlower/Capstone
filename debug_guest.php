<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new Illuminate\Http\Request([
    'reporter_type' => 'guest',
    'guest_first_name' => 'Ana',
    'guest_middle_name' => 'L.',
    'guest_last_name' => 'Reyes',
    'guest_address' => '123 Sample Street',
    'guest_contact_number' => '09181234567',
    'complainant_name' => 'Ana L. Reyes',
    'respondent_name' => 'Juan Cruz',
    'category_id' => 2,
    'subject' => 'Harassment',
    'complaint_details' => 'Threatening behavior',
    'requested_relief' => 'Protection order and mediation',
]);

$controller = $app->make(App\Http\Controllers\PublicPageController::class);

try {
    $response = $controller->submitIncidentReport($request);
    echo get_class($response), PHP_EOL;
    echo $response->getStatusCode(), PHP_EOL;
    echo $response->getTargetUrl() ?: 'NO_TARGET', PHP_EOL;
} catch (Throwable $e) {
    echo get_class($e), ': ', $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString(), PHP_EOL;
}
