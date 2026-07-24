@if (session('status') || session('success') || session('error') || session('warning') || session('info') || $errors->any())
    @php
        $messages = [
            'status'  => ['success', session('status')],
            'success' => ['success', session('success')],
            'error'   => ['danger',  session('error')],
            'warning' => ['warning', session('warning')],
            'info'    => ['info',    session('info')],
        ];
    @endphp

    @foreach ($messages as $key => $value)
        @if (! empty($value[1]))
            <div class="alert alert-{{ $value[0] }} alert-dismissible fade show" role="alert">
                {{ $value[1] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
    @endforeach

    @if ($errors->any() && ! session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Revisa los datos ingresados.</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
@endif
