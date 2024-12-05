<?php
//PHPmailer
require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';
//Clases
require_once "classes/database.php";//base de datos
require_once "classes/funciones.php";//funciones para obtener y validar los parametros
require_once "classes/correos.php";//Envio de correo
//Controladores
require_once "controller/login.php";
require_once "controller/registro.php";
require_once "controller/consultorios.php";
require_once "controller/historial_registro.php";
require_once "controller/medico.php";
require_once "controller/paciente.php";
require_once "controller/citas.php";
require_once "controller/recetas.php";
//Modelos
require_once "model/medicos.php";
require_once "model/paciente.php";
require_once "model/consultorios.php";
require_once "model/historial_clinico.php";
require_once "model/transitiva_consultorios.php";
require_once "model/citas.php";
require_once "model/recetas.php";

//Configuracion de los errores para que muestre 
error_reporting(E_ALL);
//Archivo .log
//Manda los errores a el log para que no se muestren los errores
ini_set('ignore_repeated_errors', TRUE);//Ignora los errores repetidos
ini_set('display_errors', FALSE);//Desactiva el que se muestren los errores
ini_set('log_errors', TRUE);//Habilitar el registro de errores en un archivo log
ini_set("error_log", "php.log");//Archivo donde se guardaran los errores

//Configuracion de la subida de archivos
ini_set('upload_max_filesize', '7M');//Tamaño máximo de archivo subido
ini_set('post_max_size', '15M');//Tamaño máximo de datos POST
ini_set('max_execution_time', '300');//Tiempo máximo de ejecución del script 5 minutos
ini_set('memory_limit', '128M');//Límite de memoria para el script

//CORS seguridad para el consumo de la API
$origenes_permitidos = [
    "http://localhost",//Para las pruebas en postman
    "http://localhost:4200"//Para las pruebas desde angular
];

// error_log("Cabeceras de la solicitud: " . print_r(getallheaders(), true));
// Obtiene el origen de la peticion
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';//Obtiene la cabecera del origen si no tiene se queda en blanco
// echo "$origin";

// Verificar si el origen está en la lista permitida si lo esta define los permisos
if (in_array($origin, $origenes_permitidos)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
} else {
    // Origen no permitido no tiene los permisos necesarios
    http_response_code(403);
    echo json_encode(["error" => "Sin permisos para el consumo de la api: $origin"]);
    exit;
    //die();
}

//Cambio de la fecha y zona horaria
date_default_timezone_set("America/Mexico_City");

$request = $_SERVER['REQUEST_URI']; // Obtiene la URL de la solicitud
$method = $_SERVER['REQUEST_METHOD']; // Obtiene el método HTTP si es get o post

//Descompone y obtiene la informacion de la url de la peticion
$urlComponents = parse_url($request);//Descompone la URL obtiene toda la informacion de la url
$path = $urlComponents['path'];//Path o ruta de la peticion

//Divide la url en un array para redirigir la peticion una vez elimina las barras al inicio y final del path o ruta
$busquedaPeticion = explode("/", trim($path, "/"));

//Objeto de las funciones generales este se usa generalmente para
//Obtener o validar la informacion de los parametros
//Manejar el formato de los datos
$val = new Funciones();

//Control de las peticiones

switch ($busquedaPeticion[1]) {
    //Login
    //
    //
    case 'login':
        //Valida el tipo de metodo
        if ($method === "POST") {
            $login = new Login();
            $login->validar();
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Tipo de metodo incorrecto."]);
            exit;
        }
        break;
    //Registro de los usuarios
    //
    //
    case 'registro':
        $registro = new Registro();
        switch ($busquedaPeticion[2]) {
            case 'paciente':

                error_log('paciente');
                $registro->registroPaciente();

                break;
            case 'medico':

                $registro->registroMedico();

                break;
            default:

                http_response_code(404);//Código de respuesta no encontrado
                echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);

                break;
        }
        break;
    case 'medico':
        //Informacion del medico
        //
        //
        $medico = new MedicoController();
        switch ($busquedaPeticion[2]) {
            case 'getinfo':
                if ($method === "GET") {

                    $medico->getinfo();

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            case 'update':
                if ($method === "POST") {

                    $medico->update();

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            case 'changepass':
                if ($method === "POST") {

                    $medico->changePassword();

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST ."]);
                    exit;
                }
                break;
            default:
                http_response_code(404);//Código de respuesta no encontrado
                echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);
                break;
        }
        break;
    case 'paciente':
        //Informacion del paciente
        //
        //
        $pasObj = new ControllerPaciente();
        switch ($busquedaPeticion[2]) {
            case 'getinfo':
                if ($method === "GET") {

                    $pasObj->get();//Ejecuta la funcion para obtener los datos del paciente

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            case 'update':
                if ($method === "POST") {

                    $pasObj->update();//Ejecuta la funcion para actualizar los datos del paciente

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            case 'updatepass':
                if ($method === "POST") {

                    $pasObj->updatePass();//Ejecuta la funcion para actualizar los datos del paciente

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            default:
                http_response_code(404);//Código de respuesta no encontrado
                echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);
                break;
        }
        break;
    //Historial clinico
    //
    //
    case 'historial':
        $historialC = new ControllerHisotorial();
        switch ($busquedaPeticion[2]) {
            //Obtiene todos los datos
            //
            //
            case 'getinfo'://Datos por paciente
                //Valida que el metodo sea correcto
                if ($method === "GET") {
                    $historialC->getById();//Ejecuta la funcion para obtener los datos del paciente
                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            //Emilminar un registro del historial
            //
            //
            case 'delete':
                if ($method === "POST") {
                    $historialC->delete();
                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            case 'save':
                //Guarda la informacion del historial clinico
                //Valida que el metodo sea correcto
                //
                //
                if ($method === "POST") {
                    $historialC->save();
                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            case 'documento':
                //Valida que el metodo sea correcto
                //
                //
                if ($method === "GET") {
                    $historialC->documento();//Ejecuta la funcion para obtener el documeto
                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            case 'update':
                //Metodo para actualizar un registro
                //
                //
                if ($method === "POST") {
                    $historialC->update();//Funcion para actualizar un registro
                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;
                }
                break;
            default:
                http_response_code(404);//Código de respuesta no encontrado
                echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);
                break;
        }
        break;
    //Consultorios
    //
    //
    case 'consultorio':
        $consultorios = new ControllerConsultorios();
        $citas = new citasController();
        switch ($busquedaPeticion[2]) {
            case 'getconsultorios':
                //Obtiene los consultorios cercanos
                //
                //
                if ($method === "GET") {

                    $consultorios->getConsultorios();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }
                break;
            case 'getinfocon':
                //Obtiene los datos de un consultorio
                //
                //
                if ($method === "GET") {

                    error_log("infocon");

                    $consultorios->getConsultorioByIdMedico();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }
                break;
            case 'updatecita':
                //Actualiza el estado de la cita
                //
                //
                if ($method === "POST") {

                    $citas->estadoCita();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }
                break;
            case 'horarios':
                //Obtiene los horarios disponibles
                //
                //
                if ($method === "GET") {

                    $consultorios->getHorarios();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }
                break;
            case 'newcita':
                //Registra una nueva cita
                //
                //
                if ($method === "POST") {
                    error_log('newcita');
                    $citas->newCita();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;

                }
                break;
            case 'citaspaciente':
                //Registra una nueva cita
                if ($method === "GET") {

                    $citas->getCitasIdPaciente();

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            //Citas de los medicos
            //
            //
            case 'getcitasmedico':
                //Registra una nueva cita
                if ($method === "GET") {

                    $citas->getByMedico();

                } else {
                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;
                }
                break;
            case 'recetasave':
                //Registra una nueva cita
                //
                //
                if ($method === "POST") {

                    $citas->newReceta();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un POST."]);
                    exit;

                }
                break;
            case 'getrecetasmedico':
                //Obtiene los registros de las citas de los medicos
                //
                //
                if ($method === "GET") {

                    $consultorios->getRecetasMedico();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }
            case 'getrecetaspaciente':
                //Obtiene los registros de recetas de los pacientes
                //
                //
                if ($method === "GET") {

                    $consultorios->getRecetasPaciente();

                } else {

                    http_response_code(405);
                    echo json_encode(["message" => "Tipo de metodo incorrecto se espera un GET."]);
                    exit;

                }

                break;
            default:

                http_response_code(404);//Código de respuesta no encontrado
                echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);

                break;
        }
        break;
    default:
        http_response_code(404);//Código de respuesta no encontrado
        echo json_encode(["message" => "El tipo de solicitud no fue encontrada."]);
        break;
}


?>