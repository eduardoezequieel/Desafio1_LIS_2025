/**
 * Utilidades de interfaz basadas en SweetAlert2.
 * showMessage: Muestra un modal informativo / error / éxito.
 * confirmAction: Muestra un modal de confirmación con botones Aceptar / Cancelar.
 * Todas retornan la Promesa devuelta por Swal.fire (útil para then()).
 */

/**
 * Muestra un mensaje simple (feedback al usuario).
 * Casos de entrada para textToBeParsed:
 *  - Array: Se concatena con coma y se añade un punto final.
 *  - String: Se usa directamente.
 *  - Otro / undefined: Se usa mensaje genérico.
 * No lanza excepción; siempre retorna una Promesa.
 * @param {'success'|'error'|'warning'|'info'|'question'} type Tipo de ícono.
 * @param {string} title Título del cuadro de diálogo.
 * @param {string|string[]} textToBeParsed Texto o arreglo de textos.
 * @returns {Promise<SweetAlertResult>} Promesa con el resultado del modal.
 */
const showMessage = (type, title, textToBeParsed) => {
  let text = "Ha ocurrido un error inesperado.";

  if (textToBeParsed instanceof Array) {
    text = textToBeParsed.join(", ") + ".";
  } else if (typeof textToBeParsed === "string") {
    text = textToBeParsed;
  }

  return Swal.fire({
    title,
    text,
    icon: type,
    confirmButtonText: "Aceptar",
    theme: "dark",
  });
};

/**
 * Muestra un diálogo de confirmación.
 * Retorna un objeto con { isConfirmed: boolean } entre otras propiedades.
 * Uso típico:
 *   const { isConfirmed } = await confirmAction('Titulo', 'Mensaje');
 *   if(isConfirmed){ ... }
 * @param {string} title Título del cuadro de confirmación.
 * @param {string} text Mensaje descriptivo de la acción a confirmar.
 * @returns {Promise<SweetAlertResult>}
 */
const confirmAction = (title, text) => {
  return Swal.fire({
    title,
    text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, continuar",
    cancelButtonText: "Cancelar",
    theme: "dark",
  });
};
