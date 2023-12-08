@php
    header('Content-Type: application/json');
    echo json_encode([
        'api_url' => 'http://' . request()->host() . '/conf_api',
    ], JSON_THROW_ON_ERROR);
@endphp
