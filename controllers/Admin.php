<?php

require 'vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Admin extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location:' . BASE_URL);
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }

    //reportes graficos
    public function index()
    {
        $data['title'] = 'Panel Administrativo';
        $data['script'] = 'index.js';
        $data['usuarios'] = $this->model->getTotales('usuarios');
        $data['clientes'] = $this->model->getTotales('clientes');
        $data['proveedor'] = $this->model->getTotales('proveedor');
        $data['productos'] = $this->model->getTotales('productos');
        $data['top'] = $this->model->topProductos(4);
        $data['nuevos'] = $this->model->nuevosProductos(6);

        $this->views->getView('admin', 'home', $data);
    }

    //datos d la empresa
    public function datos()
    {
        if ($_SESSION['rol'] == 2) {
            header('Location:' .    BASE_URL . 'admin/permisos');
            exit;
        }
        $data['title'] = 'Datos de la Empresa';
        $data['script'] = 'admin.js';
        $data['empresa'] = $this->model->getDatos();
        $this->views->getView('admin', 'index', $data);
    }

    //modificar datos 
    public function modificar()
    {
        if ($_SESSION['rol'] == 2) {
            header('Location:' .    BASE_URL . 'admin/permisos');
            exit;
        }
        if (isset($_POST)) {
            $ruc = strClean($_POST['ruc']);
            $nombre = strClean($_POST['nombre']);
            $telefono = strClean($_POST['telefono']);
            $correo = strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            $impuesto = strClean($_POST['impuesto']);
            $mensaje = strClean($_POST['mensaje']);
            $logo = $_FILES['foto'];
            $id = strClean($_POST['id']);
            if (empty($ruc)) {
                $res = array('msg' => 'INGRESE EL RUC', 'type' => 'warning');
            } else if (empty($nombre)) {
                $res = array('msg' => 'INGRESE EL NOMBRE', 'type' => 'warning');
            } else if (empty($telefono)) {
                $res = array('msg' => 'INGRESE EL TELÉFONO', 'type' => 'warning');
            } else if (empty($correo)) {
                $res = array('msg' => 'INGRESE EL CORREO', 'type' => 'warning');
            } else if (empty($direccion)) {
                $res = array('msg' => 'INGRESE LA DIRECCIÓN', 'type' => 'warning');
            } else {
                $data = $this->model->actualizar($ruc, $nombre, $telefono, $correo, $direccion, $impuesto, $mensaje, $id);
                if ($data == 1) {
                    if (!empty($logo['name'])) {
                        $directorio = 'assets/images/logo.png'; 
                       move_uploaded_file($logo['tmp_name'], $directorio);
                    }
                    $res = array('msg' => 'DATOS ACTUALIZADOS', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL ACTUALIZAR ', 'type' => 'error');
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO ', 'type' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    //reporte de graficos
    public function comparacion($anio)
    {
        $desde = $anio . '-01-01';
        $hasta = $anio . '-12-31';

        $data['venta'] = $this->model->calcularVentasCompras('ventas', $desde, $hasta, $this->id_usuario);
        $data['compra'] = $this->model->calcularVentasCompras('compras', $desde, $hasta, $this->id_usuario);

        $data['totalVentas'] = $this->model->totalVentasCompras('ventas', $desde, $hasta, $this->id_usuario);
        $data['totalCompras'] = $this->model->totalVentasCompras('compras', $desde, $hasta, $this->id_usuario);

        echo json_encode($data);
        die();
    }

    //top productos
    public function topProductos()
    {
        $data = $this->model->topProductos(4);

        echo json_encode($data);
        die();
    }

    //gastos 
    public function gastos($anio)
    {
        $desde = $anio . '-01-01';
        $hasta = $anio . '-12-31';

        $data = $this->model->calcularGastos($desde, $hasta, $this->id_usuario);
        echo json_encode($data);
        die();
    }

    //minimos
    public function minimosProductos()
    {
        $data = $this->model->minimosProductos();

        echo json_encode($data);
        die();
    }


    //reportes top productos
    public function topProductosPdf()
    {

        ob_start();

        $data['title'] = 'Top Productos';
        $data['empresa'] = $this->model->getEmpresa();
        $data['productos'] = $this->model->topProductos(20);
        $this->views->getView('reportes', 'reportesPdf', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function topProductosExcel()
    {

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator($_SESSION['nombre_usuario'])
            ->setTitle("Top Productos");

        $spreadsheet->setActiveSheetIndex(0);

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->getColumnDimension('A')->setWidth(50);
        $hojaActiva->getColumnDimension('B')->setWidth(10);
        $hojaActiva->getColumnDimension('C')->setWidth(20);
        $hojaActiva->getColumnDimension('D')->setWidth(20);
        $hojaActiva->getColumnDimension('E')->setWidth(30);

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('008cff');

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()
            ->getColor()->setARGB(Color::COLOR_WHITE);

        $hojaActiva->setCellValue('A1', 'Producto');
        $hojaActiva->setCellValue('B1', 'Cantidad');
        $hojaActiva->setCellValue('C1', 'Precio Compra');
        $hojaActiva->setCellValue('D1', 'Precio Venta');
        $hojaActiva->setCellValue('E1', 'Categoria');

        $fila = 2;

        $productos = $this->model->topProductos(20);
        foreach ($productos as $producto) {
            $hojaActiva->setCellValue('A' . $fila, $producto['descripcion']);
            $hojaActiva->setCellValue('B' . $fila, $producto['cantidad']);
            $hojaActiva->setCellValue('C' . $fila, $producto['precio_compra']);
            $hojaActiva->setCellValue('D' . $fila, $producto['precio_venta']);
            $hojaActiva->setCellValue('E' . $fila, $producto['categoria']);
            $fila++;
        }

        //Generar archivo Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Top Productos.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }


    //reportes stock minimo

    public function stockMinimoPdf()
    {

        ob_start();

        $data['title'] = 'Stock Minimo';
        $data['empresa'] = $this->model->getEmpresa();
        $data['productos'] = $this->model->minimosProductosPdf();
        $this->views->getView('reportes', 'reportesPdf', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function stockMinimoExcel()
    {

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator($_SESSION['nombre_usuario'])
            ->setTitle("Productos con Stock Minimo");

        $spreadsheet->setActiveSheetIndex(0);

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->getColumnDimension('A')->setWidth(50);
        $hojaActiva->getColumnDimension('B')->setWidth(10);
        $hojaActiva->getColumnDimension('C')->setWidth(20);
        $hojaActiva->getColumnDimension('D')->setWidth(20);
        $hojaActiva->getColumnDimension('E')->setWidth(30);

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('008cff');

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()
            ->getColor()->setARGB(Color::COLOR_WHITE);

        $hojaActiva->setCellValue('A1', 'Producto');
        $hojaActiva->setCellValue('B1', 'Cantidad');
        $hojaActiva->setCellValue('C1', 'Precio Compra');
        $hojaActiva->setCellValue('D1', 'Precio Venta');
        $hojaActiva->setCellValue('E1', 'Categoria');

        $fila = 2;

        $productos = $this->model->minimosProductosPdf();
        foreach ($productos as $producto) {
            $hojaActiva->setCellValue('A' . $fila, $producto['descripcion']);
            $hojaActiva->setCellValue('B' . $fila, $producto['cantidad']);
            $hojaActiva->setCellValue('C' . $fila, $producto['precio_compra']);
            $hojaActiva->setCellValue('D' . $fila, $producto['precio_venta']);
            $hojaActiva->setCellValue('E' . $fila, $producto['categoria']);
            $fila++;
        }

        //Generar archivo Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Stock Minimo de Productos.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    // pdf productos Nuevos
    public function recientesPdf()
    {

        ob_start();

        $data['title'] = 'Productos Nuevos';
        $data['empresa'] = $this->model->getEmpresa();
        $data['productos'] = $this->model->nuevosProductos(1);
        $this->views->getView('reportes', 'reportesPdf', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function recientesExcel()
    {

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setCreator($_SESSION['nombre_usuario'])
            ->setTitle("Productos Nuevos");

        $spreadsheet->setActiveSheetIndex(0);

        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->getColumnDimension('A')->setWidth(50);
        $hojaActiva->getColumnDimension('B')->setWidth(10);
        $hojaActiva->getColumnDimension('C')->setWidth(20);
        $hojaActiva->getColumnDimension('D')->setWidth(20);
        $hojaActiva->getColumnDimension('E')->setWidth(30);

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('008cff');

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()
            ->getColor()->setARGB(Color::COLOR_WHITE);

        $hojaActiva->setCellValue('A1', 'Producto');
        $hojaActiva->setCellValue('B1', 'Cantidad');
        $hojaActiva->setCellValue('C1', 'Precio Compra');
        $hojaActiva->setCellValue('D1', 'Precio Venta');
        $hojaActiva->setCellValue('E1', 'Categoria');

        $fila = 2;

        $productos = $this->model->nuevosProductos(20);
        foreach ($productos as $producto) {
            $hojaActiva->setCellValue('A' . $fila, $producto['descripcion']);
            $hojaActiva->setCellValue('B' . $fila, $producto['cantidad']);
            $hojaActiva->setCellValue('C' . $fila, $producto['precio_compra']);
            $hojaActiva->setCellValue('D' . $fila, $producto['precio_venta']);
            $hojaActiva->setCellValue('E' . $fila, $producto['categoria']);
            $fila++;
        }

        //Generar archivo Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Productos Nuevos.xlsx"');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    //logs dre acceso
    public function logs()
    {
        if ($_SESSION['rol'] == 2) {
            header('Location:' .    BASE_URL . 'admin/permisos');
            exit;
        }
        $data['title'] = 'Logs de Acceso';
        $data['script'] = 'logs.js';
        $this->views->getView('admin', 'logs', $data);
    }

    public function listarLogs()
    {
        if ($_SESSION['rol'] == 2) {
            header('Location:' .    BASE_URL . 'admin/permisos');
            exit;
        }
        $data = $this->model->listarLogs();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function limpiarDatos()
    {
        if ($_SESSION['rol'] == 2) {
            header('Location:' .    BASE_URL . 'admin/permisos');
            exit;
        }
        $data = $this->model->limpiarDatos();
        if (empty($data)) {
            $res = array('msg' => 'DATOS LIMPIADOS POR COMPLETOS', 'type' => 'success');
        } else {
            $res = array('msg' => 'ERROR AL LIMPIAR DATOS', 'type' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function permisos()
    {
        $data['title'] = 'Permisos';
        $this->views->getView('admin', 'permisos', $data);
    }
}
