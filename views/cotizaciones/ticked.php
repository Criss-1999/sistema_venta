<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/css/Tickedes.css'; ?>">
</head>

<body>
    <img src="<?php echo BASE_URL . 'assets/images/.png'; ?>" alt="">
    <div class="datos-empresa">
        <p><?php echo $data['empresa']['nombre']; ?></p>
        <p><?php echo $data['empresa']['telefono']; ?></p>
        <p><?php echo $data['empresa']['direccion']; ?></p>
    </div>
    <h5 class="title">Datos del Cliente</h5>
    <div class="datos-info">
        <p><strong><?php echo $data['cotizacion']['identidad']; ?>: </strong> <?php echo $data['cotizacion']['num_identidad']; ?></p>
        <p><strong>Nombre: </strong> <?php echo $data['cotizacion']['nombre']; ?></p>
        <p><strong>Teléfono: </strong> <?php echo $data['cotizacion']['telefono']; ?></p>
    </div>
    <h5 class="title">Detalle de los Productos</h5>
    <table>
        <thead>
            <tr>
                <th>Cant</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>SubTotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $productos = json_decode($data['cotizacion']['productos'], true);
            foreach ($productos as $producto) { ?>
                <tr>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo number_format($producto['precio'], 2); ?></td>
                    <td><?php echo number_format($producto['cantidad'] * $producto['precio'], 2); ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td class="text-right" colspan="3">Descuento</td>
                <td class="text-right"><?php echo number_format($data['cotizacion']['descuento'], 2); ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">Total sin descuento</td>
                <td class="text-right"><?php echo number_format($data['cotizacion']['total'], 2); ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">Total con descuento</td>
                <td class="text-right"><?php echo number_format($data['cotizacion']['total'] - $data['cotizacion']['descuento'], 2); ?></td>
            </tr>
           
        </tbody>
    </table>
    <div class="mensaje">
        <h4>Metodo:<?php echo $data['cotizacion']['metodo'] ?></h4>
        <h4>Validez:<?php echo $data['cotizacion']['validez'] ?></h4>
        <?php echo $data['empresa']['mensaje']; ?>
    </div>

</body>

</html>