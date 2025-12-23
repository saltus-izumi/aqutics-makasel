@props([
    'title' => 'PM Log',
])
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="tw:min-h-[100vh]">
        {{ $slot }}
    </div>
</body>
</html>
