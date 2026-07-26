import { BrowserMultiFormatReader } from '@zxing/browser';

const DEBUG = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const log = (...a) => { if (DEBUG) console.log('[Camara]', ...a); };
const error = (...a) => { if (DEBUG) console.error('[Camara]', ...a); };

class CamaraRepuestos {
  constructor(videoId) {
    this.video = document.getElementById(videoId);
    if (!this.video) throw new Error(`No se encontró video#${videoId}`);

    this.lector = new BrowserMultiFormatReader();
    this.stream = null;
    this._interval = null;
    this._deteniendo = false;
    this.camarasDisponibles = [];
    this.camaraActualId = null;
    this.onDetectado = null;

    log('Inicializada.');
  }

  async iniciar(deviceId = null) {
    this._deteniendo = false;
    this.camarasDisponibles = await this._listarCamaras();
    if (this.camarasDisponibles.length === 0) {
      return { exito: false, error: 'No se encontró ninguna cámara.' };
    }

    let camara;
    if (deviceId) {
      camara = this.camarasDisponibles.find(c => c.deviceId === deviceId);
    }
    if (!camara) {
      camara = this.camarasDisponibles.find(c =>
        c.label.toLowerCase().includes('back') || c.label.toLowerCase().includes('trasera')
      ) || this.camarasDisponibles[0];
    }

    this.camaraActualId = camara.deviceId;
    return this._iniciarStream(camara.deviceId);
  }

  async detener() {
    this._deteniendo = true;
    if (this._interval) { clearInterval(this._interval); this._interval = null; }
    if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }
    if (this.video) { this.video.srcObject = null; }
    log('Detenida.');
  }

  async cambiarCamara(deviceId) {
    await this.detener();
    return this.iniciar(deviceId);
  }

  async _listarCamaras() {
    if (!navigator.mediaDevices?.enumerateDevices) return [];
    try {
      const d = await navigator.mediaDevices.enumerateDevices();
      return d.filter(d => d.kind === 'videoinput');
    } catch { return []; }
  }

  async _iniciarStream(deviceId) {
    if (this._deteniendo) return { exito: false, error: 'Detenido' };

    try {
      const constraints = { video: { width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false };
      if (deviceId) constraints.video.deviceId = { exact: deviceId };
      else constraints.video.facingMode = 'environment';

      this.stream = await navigator.mediaDevices.getUserMedia(constraints);
      this.video.srcObject = this.stream;

      await new Promise((resolve, reject) => {
        this.video.onloadedmetadata = () => { this.video.play().then(resolve).catch(reject); };
        this.video.onerror = reject;
      });

      this._iniciarIntervalo();
      return { exito: true };
    } catch (err) {
      return { exito: false, error: this._traducirError(err) };
    }
  }

  _iniciarIntervalo() {
    if (this._deteniendo) return;
    this._interval = setInterval(async () => {
      if (this._deteniendo || !this.video || this.video.readyState < 2) return;
      try {
        const result = await this.lector.decodeOnceFromVideoElement(this.video);
        if (result) {
          const texto = result.getText().trim();
          if (texto.length > 2 && typeof this.onDetectado === 'function') {
            this.onDetectado(texto);
          }
        }
      } catch (_) { /* ZXing interno, esperado */ }
    }, 400);
  }

  _traducirError(err) {
    switch (err.name) {
      case 'NotAllowedError': return 'No se permitió el acceso a la cámara. Habilítalo desde la configuración del navegador.';
      case 'NotFoundError': return 'No se encontró ninguna cámara en este dispositivo.';
      case 'NotReadableError': return 'La cámara está siendo utilizada por otra aplicación.';
      default: return `Error de cámara: ${err.message}`;
    }
  }

  obtenerCamaras() { return this.camarasDisponibles; }
}

window.CamaraRepuestos = CamaraRepuestos;
