@props([
    'id' => 'confirmModal',
    'title' => '¿Confirmar acción?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Confirmar',
    'confirmClass' => 'btn-danger',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">{{ $message }}</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                @isset($confirm)
                    {{ $confirm }}
                @else
                    <button type="button" class="btn {{ $confirmClass }}" data-bs-dismiss="modal">{{ $confirmText }}</button>
                @endisset
            </div>
        </div>
    </div>
</div>
