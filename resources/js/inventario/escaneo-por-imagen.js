import { BrowserMultiFormatReader } from '@zxing/browser';

const lector = new BrowserMultiFormatReader();

export async function escanearImagen(archivo) {
  const url = URL.createObjectURL(archivo);

  try {
    const imagen = new Image();
    await new Promise((resolve, reject) => {
      imagen.onload = resolve;
      imagen.onerror = () => reject(new Error('No se pudo cargar la imagen.'));
      imagen.src = url;
    });

    const result = await lector.decodeFromImage(imagen);

    return {
      exito: true,
      texto: result.getText().trim(),
      formato: result.getBarcodeFormat(),
    };
  } catch (err) {
    const mensaje =
      err.message?.includes('No se pudo cargar')
        ? err.message
        : 'No se encontró ningún código válido en la imagen. Probá con otra foto o mejorá la iluminación.';

    return {
      exito: false,
      error: mensaje,
    };
  } finally {
    URL.revokeObjectURL(url);
  }
}
