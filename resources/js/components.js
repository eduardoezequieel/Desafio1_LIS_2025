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
