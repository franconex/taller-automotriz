@if (session('status') || session('success') || session('error') || session('warning') || session('info') || $errors->any())
    @php
        $alerts = [
            'status'  => ['success', 'check-circle-fill',   session('status')],
            'success' => ['success', 'check-circle-fill',   session('success')],
            'info'    => ['info',    'info-circle-fill',     session('info')],
            'warning' => ['warning', 'exclamation-triangle-fill', session('warning')],
            'error'   => ['danger',  'exclamation-octagon-fill', session('error')],
        ];
    @endphp

    <div class="admin-flash-wrapper" role="region" aria-label="Mensajes del sistema">
        @foreach ($alerts as $entry)
            @if (! empty($entry[2]))
                <div class="admin-flash admin-flash--{{ $entry[0] }}" role="alert">
                    <i class="bi bi-{{ $entry[1] }}" aria-hidden="true"></i>
                    <span>{{ $entry[2] }}</span>
                    <button type="button" class="admin-flash__close" data-bs-dismiss="alert" aria-label="Cerrar mensaje">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        @endforeach

        @if ($errors->any() && ! session('error'))
            <div class="admin-flash admin-flash--danger" role="alert">
                <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
                <span>Revisa los datos ingresados.</span>
                <button type="button" class="admin-flash__close" data-bs-dismiss="alert" aria-label="Cerrar mensaje">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
        @endif
    </div>
@endif
