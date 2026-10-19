<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryImageStorage
{
    public function store(UploadedFile $image): string
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('Cloudinary is not configured. Set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET.');
        }

        $parameters = [
            'public_id' => 'events/'.Str::uuid(),
            'timestamp' => time(),
        ];
        $signature = $this->signature($parameters, $apiSecret);
        $contents = file_get_contents($image->getRealPath());
        if ($contents === false) {
            throw new RuntimeException('Unable to read the uploaded image.');
        }

        try {
            $response = Http::attach(
                'file',
                $contents,
                $image->getClientOriginalName(),
            )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                ...$parameters,
                'api_key' => $apiKey,
                'signature' => $signature,
            ])->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('Cloudinary rejected the image upload: '.$exception->getMessage(), previous: $exception);
        }

        $secureUrl = $response->json('secure_url');

        $parsedUrl = is_string($secureUrl) ? parse_url($secureUrl) : false;
        if (! is_array($parsedUrl)) {
            throw new RuntimeException('Cloudinary did not return a valid secure image URL.');
        }

        $urlSegments = explode('/', trim($parsedUrl['path'] ?? '', '/'));
        if (($parsedUrl['scheme'] ?? null) !== 'https'
            || ($parsedUrl['host'] ?? null) !== 'res.cloudinary.com'
            || $urlSegments[0] !== $cloudName) {
            throw new RuntimeException('Cloudinary did not return a valid secure image URL.');
        }

        return $secureUrl;
    }

    public function delete(?string $secureUrl): void
    {
        if (! $secureUrl) {
            return;
        }

        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');

        $parsedUrl = parse_url($secureUrl);
        if (($parsedUrl['host'] ?? null) !== 'res.cloudinary.com') {
            return;
        }

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('Cloudinary is not configured. Set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET to delete stored images.');
        }

        $publicId = $this->publicIdFromUrl($secureUrl, $cloudName);

        if ($publicId === null) {
            throw new RuntimeException('The stored Cloudinary image URL does not belong to the configured Cloudinary account.');
        }

        $parameters = [
            'public_id' => $publicId,
            'timestamp' => time(),
        ];
        $signature = $this->signature($parameters, $apiSecret);

        Http::asForm()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
            ...$parameters,
            'api_key' => $apiKey,
            'signature' => $signature,
        ])->throw();
    }

    /**
     * @param  array<string, int|string>  $parameters
     */
    private function signature(array $parameters, string $apiSecret): string
    {
        ksort($parameters);

        $query = collect($parameters)
            ->map(fn (int|string $value, string $key): string => "{$key}={$value}")
            ->implode('&');

        return sha1($query.$apiSecret);
    }

    private function publicIdFromUrl(string $url, string $cloudName): ?string
    {
        $parsedUrl = parse_url($url);

        if (($parsedUrl['scheme'] ?? null) !== 'https' || ($parsedUrl['host'] ?? null) !== 'res.cloudinary.com') {
            return null;
        }

        $segments = explode('/', trim($parsedUrl['path'] ?? '', '/'));

        if (count($segments) < 4 || $segments[0] !== $cloudName || $segments[1] !== 'image' || $segments[2] !== 'upload') {
            return null;
        }

        $publicIdSegments = array_slice($segments, 3);
        $firstPublicIdSegment = array_shift($publicIdSegments);
        if (! preg_match('/^v\d+$/', $firstPublicIdSegment)) {
            array_unshift($publicIdSegments, $firstPublicIdSegment);
        }

        $filename = array_pop($publicIdSegments);
        if (! is_string($filename) || $filename === '') {
            return null;
        }

        $filename = preg_replace('/\.[^.]+$/', '', $filename);

        return $filename === '' ? null : implode('/', [...$publicIdSegments, $filename]);
    }
}
