@php
$manifestPath = public_path('build/manifest.json');
$cssFiles = [];
$jsFiles = [];

if (file_exists($manifestPath)) {
    $manifest = json_decode(file_get_contents($manifestPath), true);

    $entries = $entry ?? ['resources/css/app.css', 'resources/js/app.js'];

    foreach ($entries as $entry) {
        if (isset($manifest[$entry])) {
            $file = $manifest[$entry]['file'];

            if (str_ends_with($file, '.css')) {
                $cssFiles[] = $file;
            } elseif (str_ends_with($file, '.js')) {
                $jsFiles[] = $file;
            }

            if (isset($manifest[$entry]['css'])) {
                foreach ($manifest[$entry]['css'] as $css) {
                    $cssFiles[] = $css;
                }
            }
        }
    }
}
@endphp

@foreach (array_unique($cssFiles) as $css)
    <link rel="stylesheet" href="/build/{{ $css }}">
@endforeach

@foreach (array_unique($jsFiles) as $js)
    <script src="/build/{{ $js }}" defer></script>
@endforeach
