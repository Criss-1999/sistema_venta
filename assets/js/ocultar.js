function selectipo(e) {
  var tipoVenta = $("#metodo").val();
  switch (tipoVenta) {
    case "CONTADO":
      {
        $("#bltCambio").show();
        $("#bltpagar_con").show();
        
      }

      break;
    case "CREDITO":
      {
        $("#bltCambio").hide();
        $("#bltpagar_con").hide();
      }

      break;

    default:
      {
        $("#bltCambio").show();
        $("#bltpagar_con").show();
      }
      break;
  }
}


