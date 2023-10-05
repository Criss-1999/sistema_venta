const formulario = document.querySelector("#formulario");
const btnAccion = document.querySelector("#btnAccion");

const ruc = document.querySelector("#ruc");
const nombre = document.querySelector("#nombre");
const telefono = document.querySelector("#telefono");
const correo = document.querySelector("#correo");
const direccion = document.querySelector("#direccion");
const foto = document.querySelector("#foto");

const containerPreview = document.querySelector('#containerPreview');

const errorRuc = document.querySelector("#errorRuc");
const errorNombre = document.querySelector("#errorNombre");
const errorTelefono = document.querySelector("#errorTelefono");
const errorDireccion = document.querySelector("#errorDireccion");
const errorCorreo = document.querySelector("#errorCorreo");

document.addEventListener("DOMContentLoaded", function () {
  //Inicializar un Editor
  ClassicEditor.create(document.querySelector("#mensaje"), {
    toolbar: {
      items: [
        "selectAll",
        "|",
        "heading",
        "|",
        "bold",
        "italic",
        "outdent",
        "indent",
        "|",
        "undo",
        "redo",
        "alignment",
        "|",
        "link",
        "blockQuote",
        "insertTable",
        "mediaEmbed",
      ],
      shouldNotGroupWhenFull: true,
    },
  }).catch((error) => {
    console.error(error);
  });

  //vista Previa
  foto.addEventListener("change", function (e) {
    if (
      e.target.files[0].type == "image/png" 
    ) {
      const url = e.target.files[0];
      const tmpUrl = URL.createObjectURL(url);
      containerPreview.innerHTML = `<img class="img-thumbnail" src="${tmpUrl}" width="200">
        <button class="btn btn-danger" type="button" onclick="deleteImg()"><i class="fas fa-trash"></i></button>`;
    } else {
      foto.value = "";
      alertaPersonalizada(
        "warning",
        "SOLO SE PERMITEN IMG DE TIPO PNG"
      );
    }
  });

  //Actualizar datos
  formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    errorRuc.textContent = "";
    errorNombre.textContent = "";
    errorTelefono.textContent = "";
    errorDireccion.textContent = "";
    errorCorreo.textContent = "";
    if (ruc.value == "") {
      errorRuc.textContent = "INGRESE EL RUC";
    } else if (nombre.value == "") {
      errorNombre.textContent = "INGRESE EL NOMBRE";
    } else if (telefono.value == "") {
      errorTelefono.textContent = "INGRESE EL TELÉFONO";
    } else if (correo.value == "") {
      errorCorreo.textContent = "INGRESE EL CORREO";
    } else if (direccion.value == "") {
      errorDireccion.textContent = "INGRESE LA DIRECCIÓN";
    } else {
      const url = base_url + "admin/modificar";
      insertarRegistros(url, this, null, btnAccion, false);
    }
  });
});

function deleteImg() {
  foto.value = "";
  containerPreview.innerHTML = "";
}
