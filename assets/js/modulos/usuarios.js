let tblUsuarios;
const formulario = document.querySelector("#formulario");
const nombres = document.querySelector("#nombres");
const apellidos = document.querySelector("#apellidos");
const correo = document.querySelector("#correo");
const telefono = document.querySelector("#telefono");
const direccion = document.querySelector("#direccion");
const clave = document.querySelector("#clave");
const rol = document.querySelector("#rol");

const id = document.querySelector("#id");

//elemetos para mostrar errores
const errorNombre = document.querySelector("#errorNombre");
const errorApellidos = document.querySelector("#errorApellidos");
const errorCorreo = document.querySelector("#errorCorreo");
const errorTelefono = document.querySelector("#errorTelefono");
const errorDireccion = document.querySelector("#errorDireccion");
const errorClave = document.querySelector("#errorClave");
const errorRol = document.querySelector("#errorRol");

const btnAccion = document.querySelector("#btnAccion");
const btnNuevo = document.querySelector("#btnNuevo");

document.addEventListener("DOMContentLoaded", function () {
  //Cargar datos con el plugins datatables
  tblUsuarios = $("#tblUsuarios").DataTable({
    ajax: {
      url: base_url + "usuarios/listar",
      dataSrc: "",
    },
    columns: [
      { data: "nombres" },
      { data: "correo" },
      { data: "telefono" },
      { data: "direccion" },
      { data: "rol" },
      { data: "acciones" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    dom,
    buttons,
    responsive: true,
    order: [[0, "asc"]],
  });

  //limpiar usuarios
  btnNuevo.addEventListener("click", function () {
    id.value = "";
    btnAccion.textContent = "Registrar";
    clave.removeAttribute("readonly");
    formulario.reset();
    nombres.focus();
    limpiarcampos();
  });

  //registrar usuarios
  formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    limpiarcampos();
    if (nombres.value == "") {
      errorNombre.textContent = "INGRESE EL NOMBRE";
    } else if (apellidos.value == "") {
      errorApellidos.textContent = "INGRESE EL APELLIDO";
    } else if (correo.value == "") {
      errorCorreo.textContent = "INGRESE EL CORREO";
    } else if (telefono.value == "") {
      errorTelefono.textContent = "INGRESE EL TELÉFONO";
    } else if (direccion.value == "") {
      errorDireccion.textContent = "INGRESE LA DIRECCIÓN";
    } else if (clave.value == "") {
      errorClave.textContent = "INGRESE LA CONTRASEÑA";
    } else if (rol.value == "") {
      errorRol.textContent = "INGRESE EL ROL";
    } else {
      const url = base_url + "usuarios/registrar";
      insertarRegistros(url, this, tblUsuarios, btnAccion, true);
    }
  });
});

//funcion eliminar usuario
function eliminarUsuario(idUsuario) {
  const url = base_url + "usuarios/eliminar/" + idUsuario;
  eliminarRegistros(url, tblUsuarios);
}

//funcion para recuperas los datos

function editarUsuario(idUsuario) {
  limpiarcampos();
  const url = base_url + "usuarios/editar/" + idUsuario;

  //hacer una instancia del objeto XMLHttpRequest
  const http = new XMLHttpRequest();
  //abrir una conexion -POST - GET
  http.open("GET", url, true);
  //Enviar Datos
  http.send();
  //Verificar Estados
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      id.value = res.id;
      nombres.value = res.nombre;
      apellidos.value = res.apellido;
      correo.value = res.correo;
      telefono.value = res.telefono;
      direccion.value = res.direccion;
      rol.value = res.rol;
      clave.value = "0000000";
      clave.setAttribute("readonly", "readonly");
      btnAccion.textContent = "Actualizar";
      firstTab.show();
    }
  };
}

function limpiarcampos() {
  errorNombre.textContent = "";
  errorApellidos.textContent = "";
  errorCorreo.textContent = "";
  errorTelefono.textContent = "";
  errorDireccion.textContent = "";
  errorClave.textContent = "";
  errorRol.textContent = "";
}
