{{-- Modal: detalle de Cita --}}
<div class="modal fade" id="modal-detalle-cita" tabindex="-1" aria-labelledby="modal-detalle-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="h5 fw-bold mb-0" id="modal-detalle-titulo">Detalle de la cita</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modal-detalle-contenido">
                <div class="text-center text-muted py-4">Cargando…</div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: motivo (cancelar / reprogramar) --}}
<div class="modal fade" id="modal-motivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="h5 fw-bold mb-0" id="motivo-titulo">Cancelar cita</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <label for="motivo-texto" class="form-label" id="motivo-label">Motivo</label>
                <textarea id="motivo-texto" class="form-control" rows="3" minlength="3" maxlength="1000" required></textarea>
                <div class="invalid-feedback">El motivo debe tener al menos 3 caracteres.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="motivo-ok">Aceptar</button>
            </div>
        </div>
    </div>
</div>
