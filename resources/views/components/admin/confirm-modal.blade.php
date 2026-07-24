@props([
    'id' => 'confirmModal',
    'title' => '¿Confirmar acción?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'confirmClass' => 'btn-danger',
    'icon' => 'danger',
])

@php
    $iconClass = match($icon) {
        'warning' => 'admin-confirm__icon--warning',
        'info'    => 'admin-confirm__icon--info',
        default   => '',
    };
    $iconBi = match($icon) {
        'warning' => 'bi-exclamation-triangle',
        'info'    => 'bi-info-circle',
        default   => 'bi-exclamation-triangle',
    };
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center px-4 pt-4 pb-3">
                <div class="admin-confirm__icon {{ $iconClass }}">
                    <i class="bi {{ $iconBi }}" aria-hidden="true"></i>
                </div>
                <h2 class="h5 fw-bold mb-2" id="{{ $id }}Label">{{ $title }}</h2>
                <p class="text-muted mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ $cancelText }}</button>
                <button type="button" class="btn {{ $confirmClass }}" data-bs-dismiss="modal">{{ $confirmText }}</button>
            </div>
        </div>
    </div>
</div>
