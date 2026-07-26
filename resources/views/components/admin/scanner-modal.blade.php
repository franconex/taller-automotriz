@props([
    'id' => 'adminScannerModal',
    'title' => 'Escanear código de barras',
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="{{ $id }}Label">
                    <i class="bi bi-upc-scan" aria-hidden="true"></i>
                    {{ $title }}
                </h2>
                <button type="button" class="btn-close" onclick="tpCerrarScanner()" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div id="tpScannerVideoWrap" style="position:relative;max-width:100%;background:#000;border-radius:8px;overflow:hidden;display:none;">
                        <video id="tpScannerVideo" autoplay playsinline muted style="width:100%;height:auto;max-height:300px;display:block;object-fit:cover;"></video>
                        <div style="position:absolute;top:50%;left:5%;right:5%;height:2px;background:linear-gradient(90deg,transparent,#0d6efd,transparent);transform:translateY(-50%);animation:tp-scanline 1.5s linear infinite;"></div>
                    </div>
                    <div id="tpScannerNoCamera" class="alert alert-warning" style="display:none;">
                        <i class="bi bi-camera-video-off" aria-hidden="true"></i>
                        No se pudo acceder a la cámara. Escribí el código manualmente.
                    </div>
                </div>

                <div class="input-group input-group-lg">
                    <span class="input-group-text"><i class="bi bi-upc-scan" aria-hidden="true"></i></span>
                    <input type="text" id="tpScannerInput" class="form-control text-center"
                           autocomplete="off" placeholder="Código de barras"
                           style="letter-spacing:2px;font-size:1.3rem;">
                    <button type="button" class="btn btn-primary" onclick="tpAceptarScanner()">
                        <i class="bi bi-check2"></i>
                        Aceptar
                    </button>
                </div>
                <div class="form-text text-center mt-2">
                    <i class="bi bi-info-circle"></i>
                    Apuntá la cámara al código de barras o escribilo manualmente.
                    Si el producto ya existe, se abrirá su ficha.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="tpCerrarScanner()">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes tp-scanline {
    0% { top: 5%; }
    50% { top: 95%; }
    100% { top: 5%; }
}
</style>

@once
@push('scripts')
<script>
// Variables globales del escáner
var tpScannerStream = null;
var tpScannerInterval = null;
var tpScannerCallback = null;

function abrirEscanner(callback) {
    tpScannerCallback = callback;
    var modal = document.getElementById('adminScannerModal');
    if (!modal) return;

    modal.classList.add('show');
    modal.style.display = 'block';
    modal.setAttribute('aria-modal', 'true');
    modal.removeAttribute('aria-hidden');
    document.body.classList.add('modal-open');

    if (!document.querySelector('.modal-backdrop')) {
        var bd = document.createElement('div');
        bd.className = 'modal-backdrop fade show';
        document.body.appendChild(bd);
    }

    var input = document.getElementById('tpScannerInput');
    if (input) { input.value = ''; input.focus(); }

    tpIniciarCamara();
}

function tpCerrarScanner() {
    tpPararCamara();
    var modal = document.getElementById('adminScannerModal');
    if (!modal) return;
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(function(e) { e.remove(); });
}

function tpAceptarScanner() {
    var input = document.getElementById('tpScannerInput');
    if (!input) return;
    var codigo = input.value.trim();
    if (!codigo) return;
    tpPararCamara();
    tpCerrarScanner();
    if (tpScannerCallback) tpScannerCallback(codigo);
}

function tpIniciarCamara() {
    var wrap = document.getElementById('tpScannerVideoWrap');
    var video = document.getElementById('tpScannerVideo');
    var nocam = document.getElementById('tpScannerNoCamera');
    if (!wrap || !video) return;

    if (nocam) nocam.style.display = 'none';
    wrap.style.display = 'none';

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (nocam) nocam.style.display = '';
        return;
    }

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
        .then(function(stream) {
            tpScannerStream = stream;
            video.srcObject = stream;
            wrap.style.display = '';

            if ('BarcodeDetector' in window) {
                window.BarcodeDetector.getSupportedFormats().then(function(formats) {
                    var detector = new BarcodeDetector({ formats: formats });
                    tpScannerInterval = setInterval(function() {
                        detector.detect(video).then(function(codes) {
                            if (codes.length > 0) {
                                var c = codes[0].rawValue.trim();
                                if (c && c.length > 2) {
                                    tpPararCamara();
                                    tpCerrarScanner();
                                    if (tpScannerCallback) tpScannerCallback(c);
                                }
                            }
                        }).catch(function() {});
                    }, 400);
                }).catch(function() {});
            }
        })
        .catch(function(err) {
            if (nocam) nocam.style.display = '';
            wrap.style.display = 'none';
        });
}

function tpPararCamara() {
    if (tpScannerInterval) {
        clearInterval(tpScannerInterval);
        tpScannerInterval = null;
    }
    if (tpScannerStream) {
        tpScannerStream.getTracks().forEach(function(t) { t.stop(); });
        tpScannerStream = null;
    }
    var video = document.getElementById('tpScannerVideo');
    if (video && video.srcObject) video.srcObject = null;
}
</script>
@endpush
@endonce
