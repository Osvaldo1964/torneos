/**
 * Ayudantes Globales - Global Cup
 * Este script contiene funciones reutilizables para toda la aplicación.
 */

/**
 * Genera el encabezado estándar para todos los documentos de impresión (Recibos, Cuotas, Balances, etc.)
 * @param {Object} data Objeto con nombre_liga, nombre_torneo, logo
 * @returns {String} HTML del encabezado
 */
function generarEncabezadoPrint(data) {
    const liga = data.nombre_liga || "Global Cup";
    const torneo = data.nombre_torneo || "";

    // Prioridad de logos: Torneo > Liga > Default
    let logoPath = data.logo || data.liga_logo || "default_torneo.png";
    let folder = (data.logo) ? 'torneos' : (data.liga_logo ? 'logos' : 'torneos');

    // app_config.base_url es usualmente http://localhost/torneos/
    // Las imágenes están en app/assets/images/
    const logoUrl = `${app_config.base_url}app/assets/images/${folder}/${logoPath}`;

    return `
        <div class="document-print-header" style="display: flex; align-items: center; border-bottom: 3px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; width: 100%;">
            <div style="flex: 0 0 100px; margin-right: 25px;">
                <img src="${logoUrl}" alt="Logo" style="width: 100px; height: 100px; object-fit: contain;">
            </div>
            <div style="flex: 1;">
                <h2 style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; font-weight: 800; text-transform: uppercase; color: #333; font-size: 20px;">${liga}</h2>
                <h4 style="margin: 2px 0 0 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; font-weight: 600; color: #666; font-size: 15px;">${torneo}</h4>
                <div style="margin-top: 8px;">
                    <span style="background: #0d6efd; color: white; padding: 2px 10px; border-radius: 4px; font-size: 10px; font-family: Arial; font-weight: bold; text-transform: uppercase;">Documento Oficial de Competición</span>
                </div>
            </div>
            <div style="text-align: right; flex: 0 0 150px;">
                <p style="margin: 0; font-family: Arial; font-size: 9px; color: #999;">Fecha Impresión:</p>
                <p style="margin: 0; font-family: Arial; font-size: 11px; font-weight: bold;">${new Date().toLocaleDateString()}</p>
            </div>
        </div>
    `;
}

/**
 * Formatea un monto a moneda local
 */
function formatMoney(amount) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

/**
 * Formatea una fecha (YYYY-MM-DD) a formato legible
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    // Si ya trae hora (YYYY-MM-DD HH:mm:ss), reemplazamos espacio por T para parseo ISO
    let cleanDate = dateString.includes(' ') ? dateString.replace(' ', 'T') : dateString + 'T00:00:00';
    const date = new Date(cleanDate);

    if (isNaN(date.getTime())) return dateString; // Si falla, devolvemos el string original

    return date.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

/**
 * Realiza una petición fetch estándar a la API con manejo de errores y token
 * @param {string} endpoint Ruta del endpoint (ej: 'Finanzas/balance')
 * @param {object} options Opciones de fetch (method, body, etc)
 */
async function fetchAPI(endpoint, options = {}) {
    const defaultHeaders = {
        'Authorization': `Bearer ${localStorage.getItem('gc_token')}`
    };

    // Si enviamos body JSON, agregamos el content-type
    if (options.body && typeof options.body === 'string') {
        defaultHeaders['Content-Type'] = 'application/json';
    }

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    try {
        const response = await fetch(`${app_config.api_url}${endpoint}`, config);

        if (response.status === 401) {
            Swal.fire({
                title: 'Sesión Expirada',
                text: 'Tu sesión ha caducado. Por favor inicia sesión nuevamente.',
                icon: 'warning',
                confirmButtonText: 'Ir al Login',
                allowOutsideClick: false
            }).then(() => {
                window.location.href = '../login.php';
            });
            throw new Error('Sesión Expirada');
        }

        return await response.json();
    } catch (error) {
        console.error(`API Error (${endpoint}):`, error);
        throw error;
    }
}

/**
 * Muestra alerta de éxito
 */
function swalSuccess(msg, title = 'Éxito') {
    return Swal.fire({
        title: title,
        text: msg,
        icon: 'success',
        confirmButtonColor: '#3085d6'
    });
}

/**
 * Muestra alerta de error
 */
function swalError(msg, title = 'Error') {
    return Swal.fire({
        title: title,
        text: msg,
        icon: 'error',
        confirmButtonColor: '#d33'
    });
}

/**
 * Muestra alerta de carga
 */
function swalLoading(title = 'Procesando...', text = 'Por favor espere') {
    Swal.fire({
        title: title,
        text: text,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Muestra confirmación
 */
function swalConfirm(title, text, confirmText = 'Sí, continuar') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar'
    });
}
