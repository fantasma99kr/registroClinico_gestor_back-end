<?php

 //Controlador de los consultorios
 //
 //

 class ControllerConsultorios{

    private $fun;
    private $cconsultorios;
    private $ccitas;
    private $recetas;

    //Constructor
    //
    //
    public function __construct(){
        $this->fun = new Funciones();
        $this->cconsultorios = new ConsultorioModel();
        $this->ccitas = new CitaModel();
        $this->recetas = new RecetaModel();
    }

    //Funcion para obtener todos los datos de los consultorios
    //
    //
    public function getConsultorios(){
        //Manda todos los datos
        $data = $this->cconsultorios->getAll();
        http_response_code(201);
        echo json_encode($this->cconsultorios->getAll());
        exit;
    }

    //Obtiene los datos del consultorio por el id del medico
    //
    //
    public function getConsultorioByIdMedico(){
        //Verifica que se manden todos los paramentros
        if($this->fun->existGET(['id_medico'])){

            $data = $this->cconsultorios->getConsultorioByIdMedico($this->fun->getGet('id_medico'));
            echo $data;
            http_response_code(200);
            exit;
        } else {
            http_response_code(400); 
            echo json_encode(["message" => "Faltan parametros"]); 
            exit;
        }
    }

    //Obtiener los consultorios cercanos
    //
    //
    // public function getNearConsultorio(){

    //     //Verifica que se manden todos los paramentros
    //     if($this->fun->existGET(['longitud'])){
            
    //     } else {
    //         http_response_code(400); 
    //         echo json_encode(["message" => "Faltan parametros"]); 
    //         exit;
    //     }
    // }

    //Actualizar los datos del consultorio
    //
    //
    // public function updateConsultorio(){
    //     //Actualiza los datos dle consultorio
    //     if($this->fun->existPOST(['id_consultorio', 'nombre', 'telefono', 'correo'])){


    //     } else {
    //         http_response_code(400);
    //         echo json_encode(["message" => "Faltan parametros"]);
    //         exit;
    //     }
    // }

    //Obtener el registro de recetas de un paciente
    //
    //
    public function getRecetasPaciente(){
        //Verifica que se mande el parametro
        if($this->fun->existGET(['id_paciente'])){
            $this->recetas->setIdPaciente($this->fun->getGet("id_paciente"));
            $data = $this->recetas->getAllByPaciente();

            echo json_encode($data);
            http_response_code(200);
            exit;

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }

    //Obtiene las recetas de un consultorio
    //
    //
    public function getRecetasMedico(){
        //Verifica que se mande el parametro
        if($this->fun->existGET(['id_medico'])){
            $this->recetas->setIdMedico($this->fun->getGet('id_medico'));
            $data  = $this->recetas->getRecetasMedico();
            //error_log(json_encode($data));
            http_response_code(200);
            echo json_encode($data);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }


    //Obtener los horarios disponibles
    //
    //
    public function getHorarios(){
        if($this->fun->existGET(['id_consultorio','fecha'])){

            $id_consultorio = $this->fun->getGet('id_consultorio');
            $fecha = $this->fun->getGet('fecha');
            //error_log($id_consultorio);
            //error_log($fecha);
            
            $horarios = $this->ccitas->getHorariosDisponibles($id_consultorio, $fecha);

            http_response_code(200); 
            echo json_encode($horarios); 
            //error_log(json_encode($horarios));
            exit;

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }
 }



?>