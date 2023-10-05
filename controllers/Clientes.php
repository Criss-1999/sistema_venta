<?php

class Clientes extends Controller
{

    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location:' . BASE_URL);
            exit;
         }
    }

    public function index()
    {
        $data['title'] = 'Clientes';
        $data['script'] = 'clientes.js';
        $this->views->getView('clientes', 'index', $data);
    }

    public function listar()
    {
        $data = $this->model->getClientes(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['acciones'] = '<div>
            <button class="btn btn-danger" type="button" onclick="eliminarClientes(' . $data[$i]['id'] . ')" ><i class="fas fa-trash"></i></button>
            <button class="btn btn-info" type="button" onclick="editarClientes(' . $data[$i]['id'] . ')" ><i class="fas fa-edit"></i></button>
            </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['identidad']) && isset($_POST['num_identidad'])) {
            $id = strClean($_POST['id']);
            $identidad = strClean($_POST['identidad']);
            $num_identidad = strClean($_POST['num_identidad']);
            $nombre = strClean($_POST['nombre']);
            $telefono = strClean($_POST['telefono']);
            $correo  = (empty($_POST['correo'])) ? null : strClean($_POST['correo']);;
            $direccion = strClean($_POST['direccion']);
            if (empty($identidad)) {
                $res = array('msg' => 'INGRESE LA IDENTIDAD', 'type' => 'warning');
            } else if (empty($num_identidad)) {
                $res = array('msg' => 'INGRESE N° DE IDENTIDAD', 'type' => 'warning');
            } else if (empty($nombre)) {
                $res = array('msg' => 'INGRESE EL NOMBRE', 'type' => 'warning');
            } else if (empty($telefono)) {
                $res = array('msg' => 'INGRESE EL TELEFONO', 'type' => 'warning');
            } else if (empty($direccion)) {
                $res = array('msg' => 'INGRESE LA DIRECCION', 'type' => 'warning');
            } else {
                if ($id == '') {
                    $vereficarIdentidad = $this->model->getValidar('num_identidad', $num_identidad, 'registrar', 0);
                    if (empty($vereficarIdentidad)) {
                        $vereficarTelefono = $this->model->getValidar('telefono', $telefono, 'registrar', 0);
                        if (empty($vereficarTelefono)) {
                            if ($correo != null) {
                                $vereficarCorreo = $this->model->getValidar('correo', $correo, 'registrar', 0);
                                if (!empty($vereficarCorreo)) {
                                    $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'type' => 'warning');
                                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                                    die();
                                }
                            }
                            $data = $this->model->registrar($identidad, $num_identidad, $nombre, $telefono, $correo, $direccion);
                            if ($data > 0) {
                                $res = array('msg' => 'CLIENTE REGISTRADO', 'type' => 'success');
                            } else {
                                $res = array('msg' => 'ERROR AL REGISTRAR', 'type' => 'error');
                            }
                        } else {
                            $res = array('msg' => 'EL TELEFONO DEBE SER UNICO', 'type' => 'warning');
                        }
                    } else {
                        $res = array('msg' => 'LA IDENTIDAD DEBE SER UNICA', 'type' => 'warning');
                    }
                } else {
                    $vereficarIdentidad = $this->model->getValidar('num_identidad', $num_identidad, 'actualizar', $id);
                    if (empty($vereficarIdentidad)) {
                        $vereficarTelefono = $this->model->getValidar('telefono', $telefono, 'actualizar', $id);
                        if (empty($vereficarTelefono)) {
                            if ($correo != null) {
                                $vereficarCorreo = $this->model->getValidar('correo', $correo, 'actualizar', $id);
                                if (!empty($vereficarCorreo)) {
                                    $res = array('msg' => 'EL CORREO DEBE SER UNICO', 'type' => 'warning');
                                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                                    die();
                                }
                            }
                            $data = $this->model->actualizar($identidad, $num_identidad, $nombre, $telefono, $correo, $direccion, $id);
                            if ($data > 0) {
                                $res = array('msg' => 'CLIENTE ACTUALIZADO', 'type' => 'success');
                            } else {
                                $res = array('msg' => 'ERROR AL ACTUALIZAR', 'type' => 'error');
                            }
                        } else {
                            $res = array('msg' => 'EL TELEFONO DEBE SER UNICO', 'type' => 'warning');
                        }
                    } else {
                        $res = array('msg' => 'LA IDENTIDAD DEBE SER UNICA', 'type' => 'warning');
                    }
                }
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function eliminar($idCliente)
    {
        if (isset($_GET) && is_numeric($idCliente)) {
            $data = $this->model->eliminar(0, $idCliente);
            if ($data > 0) {
                $res = array('msg' => 'CLIENTE DADO DE BAJA', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL ELIMINAR', 'type' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function editar($idCliente)
    {
        $data = $this->model->editar($idCliente);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function inactivos()
    {
        $data['title'] = 'Clientes Inactivos';
        $data['script'] = 'clientes-inactivos.js';
        $this->views->getView('clientes', 'inactivos', $data);
    }

    public function listarInactivos()
    {
        $data = $this->model->getClientes(0);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['acciones'] = '<div>
            <button class="btn btn-danger" type="button" onclick="restaurarCliente(' . $data[$i]['id'] . ')" ><i class="fas fa-check-circle"></i></button>
            </div>';
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function restaurar($idCliente)
    {
        if (isset($_GET) && is_numeric($idCliente)) {
            $data = $this->model->eliminar(1, $idCliente);
            if ($data == 1) {
                $res = array('msg' => 'CLIENTE HA SIDO RESTAURADA', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL RESTAURAR', 'type' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    //buscar cliente para la venta
    public function buscar()
    {
        $array = array();
        $valor = strClean($_GET['term']);
        $data = $this->model->buscarPorNombre($valor);
        foreach ($data as $row) {
            $result['id'] = $row['id'];
            $result['label'] = $row['nombre'];
            $result['telefono'] = $row['telefono'];
            $result['direccion'] = $row['direccion'];
            array_push($array, $result);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }
}
