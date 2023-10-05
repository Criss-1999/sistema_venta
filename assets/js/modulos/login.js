const formulario = document.querySelector("#formulario");
const correo = document.querySelector("#correo");
const clave = document.querySelector("#clave");

const errorCorreo = document.querySelector("#errorCorreo");
const errorClave = document.querySelector("#errorClave");

document.addEventListener("DOMContentLoaded", function () {
  formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    errorCorreo.textContent = "";
    errorClave.textContent = "";
    if (correo.value == "") {
      errorCorreo.textContent = "INGRESE EL CORREO";
    } else if (clave.value == "") {
      errorClave.textContent = "INGRESE LA CONTRASEÑA";
    } else {
      const url = base_url + "principal/validar";
      //crear formdata
      const data = new FormData(this);
      //hacer una instancia del objeto XMLHttpRequest
      const http = new XMLHttpRequest();
      //abrir una conexion -POST - GET
      http.open("POST", url, true);
      //Enviar Datos
      http.send(data);
      //Verificar Estados
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          Swal.fire({
            toast: true,
            position: "top-center",
            icon: res.type,
            title: res.msg,
            showConfirmButton: false,
            timer: 2500,
          });
          if (res.type == "success") {
            setTimeout(() => {
              let timerInterval;
              Swal.fire({
                title: "BIENVENIDO",
                html: "Espere unos <b></b> segundos.",
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading();
                  const b = Swal.getHtmlContainer().querySelector("b");
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft();
                  }, 100);
                },
                willClose: () => {
                  clearInterval(timerInterval);
                },
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  window.location = base_url + "admin";
                }
              });
            }, 2000);
          }
        }
      };
    }
  });
});
