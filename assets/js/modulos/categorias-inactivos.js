let tblCategorias;
document.addEventListener("DOMContentLoaded", function () {
    //Cargar datos con el plugins datatables
    tblCategorias = $("#tblCategorias").DataTable({
        ajax: {
          url: base_url + "categorias/listarInactivos",
          dataSrc: "",
        },
        columns: [
            { data: "categoria" },
          { data: "fecha" },
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
   })
   
   //funcion eliminar usuario
   function restaurarCategoria(idCategoria) {
     const url = base_url + "categorias/restaurar/" + idCategoria;
     restaurarRegistros(url, tblCategorias);
     }