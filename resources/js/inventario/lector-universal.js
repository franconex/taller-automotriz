const DEBUG = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const log = (...a) => { if (DEBUG) console.log('[LectorUniversal]', ...a); };
const COOLDOWN_MS = 800;

class LectorUniversal {
  constructor(opciones = {}) {
    this.inputId = opciones.inputId || 'lector-input';
    this.resultadoId = opciones.resultadoId || 'lector-resultado';
    this.buscarUrl = opciones.buscarUrl || '/admin/repuestos/escaner/buscar';
    this.entradaUrl = opciones.entradaUrl || '/admin/inventario/entrada';
    this.onResultado = opciones.onResultado || null;
    this.onError = opciones.onError || null;

    this.input = null;
    this.resultado = null;
    this._bloqueado = false;
    this._ultimoCodigo = '';
    this._ultimoTimestamp = 0;
    this._guardando = false;
  }

  iniciar() {
    this.input = document.getElementById(this.inputId);
    this.resultado = document.getElementById(this.resultadoId);
    if (!this.input) { log('Input no encontrado'); return; }

    this.input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        this.procesar();
      }
    });

    this._enfocar();

    document.addEventListener('click', () => this._enfocar());
    document.addEventListener('focusin', (e) => {
      if (e.target && e.target !== this.input && e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT' && e.target.tagName !== 'TEXTAREA') {
        setTimeout(() => this._enfocar(), 100);
      }
    });

    log('Iniciado. Input:', this.input.id);
  }

  _enfocar() {
    if (this.input && !this._guardando && document.activeElement !== this.input) {
      this.input.focus();
    }
  }

  procesar() {
    const codigo = (this.input.value || '').trim();
    if (!codigo || this._bloqueado || this._guardando) return;

    const ahora = Date.now();
    if (codigo === this._ultimoCodigo && (ahora - this._ultimoTimestamp) < COOLDOWN_MS) return;

    this._ultimoCodigo = codigo;
    this._ultimoTimestamp = ahora;
    this._bloqueado = true;

    this._reproducirSonido();
    this._mostrarEstado('Buscando…');
    this._buscar(codigo);
  }

  async _buscar(codigo) {
    try {
      const res = await fetch(`${this.buscarUrl}?codigo=${encodeURIComponent(codigo)}`);
      if (!res.ok) throw new Error(`Error ${res.status}`);
      const data = await res.json();

      if (data.encontrado) {
        this._mostrarResultado(data.repuesto);
        if (typeof this.onResultado === 'function') this.onResultado(data.repuesto);
      } else {
        this._mostrarNoEncontrado(codigo, data.mensaje);
      }
    } catch (err) {
      this._mostrarError('Error al buscar. Verificá la conexión.');
      if (typeof this.onError === 'function') this.onError(err);
    } finally {
      this._bloqueado = false;
    }
  }

  _mostrarResultado(rep) {
    if (!this.resultado) return;
    const tieneStock = rep.stock_disponible > 0;

    this.resultado.innerHTML = `
      <div class="alert alert-success d-flex align-items-start gap-3 py-3 mb-3">
        <i class="bi bi-check-circle-fill fs-4 text-success flex-shrink-0 mt-1"></i>
        <div class="w-100">
          <div class="fw-bold mb-1">${this._escape(rep.nombre)}</div>
          <div class="small text-muted mb-2">
            ${rep.codigo_barras ? 'Código: ' + this._escape(rep.codigo_barras) + ' · ' : ''}
            ${rep.marca ? this._escape(rep.marca) + ' · ' : ''}
            ${rep.categoria ? this._escape(rep.categoria) : ''}
          </div>
          <div class="mb-2">
            Stock actual: <strong class="${tieneStock ? 'text-success' : 'text-danger'}">${rep.stock_disponible}</strong>
          </div>
          <form id="form-entrada-lector" class="row g-2 bg-light p-3 rounded">
            <input type="hidden" name="repuesto_id" value="${rep.id}">
            <div class="col-6 col-md-3">
              <label class="form-label small">Sucursal</label>
              <select name="sucursal_id" class="form-select form-select-sm" required>
                ${document.getElementById('lector-sucursales')?.innerHTML || '<option>Seleccionar</option>'}
              </select>
            </div>
            <div class="col-6 col-md-2">
              <label class="form-label small">Cantidad</label>
              <input type="number" name="cantidad" class="form-control form-control-sm" min="1" required autocomplete="off">
            </div>
            <div class="col-6 col-md-2">
              <label class="form-label small">Precio Bs</label>
              <input type="number" step="0.01" name="precio_unitario" class="form-control form-control-sm" min="0" autocomplete="off">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label small">Proveedor</label>
              <select name="proveedor_id" class="form-select form-select-sm">
                <option value="">— Sin proveedor —</option>
                ${document.getElementById('lector-proveedores')?.innerHTML || ''}
              </select>
            </div>
            <div class="col-6 col-md-2">
              <label class="form-label small">Factura</label>
              <input type="text" name="factura" class="form-control form-control-sm" autocomplete="off">
            </div>
            <div class="col-12">
              <label class="form-label small">Observaciones</label>
              <input type="text" name="observaciones" class="form-control form-control-sm" autocomplete="off">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
              <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-check2"></i> Confirmar entrada
              </button>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-escanear-otro">
                <i class="bi bi-arrow-repeat"></i> Escanear otro
              </button>
            </div>
          </form>
        </div>
      </div>
    `;
    this.resultado.style.display = '';

    const form = document.getElementById('form-entrada-lector');
    if (form) {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        this._confirmarEntrada(form);
      });
    }

    document.getElementById('btn-escanear-otro')?.addEventListener('click', () => this._limpiar());
  }

  _mostrarNoEncontrado(codigo, mensaje) {
    if (!this.resultado) return;

    this.resultado.innerHTML = `
      <div class="alert alert-warning d-flex align-items-start gap-3 py-3 mb-3">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0 mt-1"></i>
        <div class="w-100">
          <div class="fw-bold mb-1">Código ${this._escape(codigo)}</div>
          <p class="small mb-2">${this._escape(mensaje || 'El código no está registrado.')}</p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" onclick="window.lectorUniversal?.abrirRegistro('${this._escapeJs(codigo)}')">
              <i class="bi bi-plus-lg"></i> Registrar repuesto
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.lectorUniversal?._limpiar()">
              <i class="bi bi-arrow-repeat"></i> Escanear otro
            </button>
          </div>
        </div>
      </div>
    `;
    this.resultado.style.display = '';
  }

  _mostrarError(mensaje) {
    if (!this.resultado) return;
    this.resultado.innerHTML = `<div class="alert alert-danger py-2 small mb-3">${this._escape(mensaje)}</div>`;
    this.resultado.style.display = '';
  }

  _mostrarEstado(mensaje) {
    if (!this.resultado) return;
    this.resultado.innerHTML = `<div class="text-muted small py-2 mb-3"><i class="bi bi-arrow-repeat"></i> ${this._escape(mensaje)}</div>`;
    this.resultado.style.display = '';
  }

  async _confirmarEntrada(form) {
    if (this._guardando) return;
    this._guardando = true;
    this._mostrarEstado('Guardando entrada…');

    const datos = {};
    const fd = new FormData(form);
    for (const [k, v] of fd) { datos[k] = v; }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    try {
      const res = await fetch(this.entradaUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(datos),
      });

      if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || `Error ${res.status}`);
      }

      const data = await res.json();
      this._mostrarEstado(`<span class="text-success fw-semibold">${this._escape(data.mensaje || 'Entrada registrada')}</span>`);
      setTimeout(() => this._limpiar(), 800);
    } catch (err) {
      this._mostrarError(err.message || 'Error al guardar la entrada.');
    } finally {
      this._guardando = false;
    }
  }

  _limpiar() {
    if (this.input) { this.input.value = ''; }
    if (this.resultado) { this.resultado.style.display = 'none'; this.resultado.innerHTML = ''; }
    this._ultimoCodigo = '';
    this._guardando = false;
    setTimeout(() => this._enfocar(), 100);
  }

  abrirRegistro(codigo) {
    const input = document.getElementById('ef_codigo_barras');
    const label = document.getElementById('ef_codigo_label');
    if (input) input.value = codigo;
    if (label) label.textContent = codigo;
    window.tpAbrirModal('modalFormEscaner');
    setTimeout(() => document.getElementById('ef_nombre')?.focus(), 200);
  }

  _reproducirSonido() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.frequency.value = 1200;
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      osc.start();
      osc.stop(ctx.currentTime + 0.15);
    } catch (e) { /* sin audio */ }
  }

  _escape(str) { if (!str) return ''; return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
  _escapeJs(str) { if (!str) return ''; return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'"); }
}

window.LectorUniversal = LectorUniversal;
export default LectorUniversal;
