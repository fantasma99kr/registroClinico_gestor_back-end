<?php

class citasController
{

    //Funciones
    private $fun;
    //Objeto del modelo
    private $ccitas;
    private $medico;
    private $receta;
    //Correo
    private $mail;
    //Transitiva
    private $transitiva;

    //Constructor
    public function __construct()
    {
        $this->fun = new Funciones();
        $this->ccitas = new CitaModel();
        $this->medico = new MedicoModel();
        $this->consultorio = new ConsultorioModel();
        $this->emaillib = new Email();
        $this->transitiva = new TransitivaConsultoriosModel();
        $this->receta = new RecetaModel();
        
    }

    //Registro de nueva cita
    //
    //
    public function newCita()
    {
        //Verifica que se manden todos los parametros
        if ($this->fun->existPOST(['id_paciente', 'id_consultorio', 'horario', 'permiso_historial', 'fecha', 'datos_adicionales', 'tipo_cita'])) {

                $id_consultorio = $this->fun->getPost('id_consultorio');
                $fecha = $this->fun->getPost('fecha');
                $horario = $this->fun->getPost('horario');

                //Obtiene los datos
                $this->ccitas->setIdPaciente($this->fun->getPost('id_paciente'));
                $this->ccitas->setIConsultorio($id_consultorio);
                $this->ccitas->setHorario($this->fun->getPost('horario'));
                $this->ccitas->setPermisoHistorial($this->fun->getPost('permiso_historial'));
                $this->ccitas->setHorario($horario);
                $this->ccitas->setFecha($fecha);
                $this->ccitas->setDatosAdicionales($this->fun->getPost('datos_adicionales'));
                $this->ccitas->setTipo_cita($this->fun->getPost('tipo_cita'));
                //Realiza el registro de la cita
                $this->ccitas->new();
                //Obtiene los datos del medico por el id del consultorio
                $this->medico->getByConsulId($id_consultorio);
                //Obtiene el correo del medico
                $email = $this->medico->getCorreo();
                //Se envia un correo para notificar al medico
                $this->emaillib->newCita($email,$fecha,$horario);
                //Manda la respuesta de que se realizo el registro correctamente
                http_response_code(201);
                echo json_encode(["message" => "ok"]);
                exit;
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }

    public function newReceta(){
        //Verificar si faltan parametros
        error_log("new");
        if($this->fun->existPOST(['id_paciente','id_medico' ,'id_consultorio', 'edad', 'estatura', 'peso', 'diagnostico', 'fecha', 'alergias', 'temperatura', 'tratamiento', 't_a'])){
            $this->receta->setIdMedico($this->fun->getPost('id_medico'));
            $this->receta->setIdConsultorio($this->fun->getPost('id_consultorio'));
            $this->receta->setIdPaciente($this->fun->getPost('id_paciente'));
            $this->receta->setEdad($this->fun->getPost('edad'));
            $this->receta->setEstatura($this->fun->getPost('estatura'));
            $this->receta->setPeso($this->fun->getPost('peso'));
            $this->receta->setDiagnostico($this->fun->getPost('diagnostico'));
            $this->receta->setFecha($this->fun->getPost('fecha'));
            $this->receta->setAlergias($this->fun->getPost('alergias'));
            $this->receta->setTemperatura($this->fun->getPost('temperatura'));
            $this->receta->setTratamiento($this->fun->getPost('tratamiento'));
            $this->receta->setTA($this->fun->getPost('t_a'));

            $this->receta->new();

            http_response_code(201);
            echo json_encode(["message" => "Registro realizado con exito"]);  
            exit;

        } else {
            http_response_code(400); 
            echo json_encode(["message" => "Faltan parametros"]); 
            exit;
        }
    }

    //Actualiza el estado de la cita
    //
    //
    public function estadoCita()
    {
        if ($this->fun->existPOST(['id_cita', 'estado'])) {
            $this->ccitas->setIdCita($this->fun->getPost('id_cita'));
            $this->ccitas->setEstado($this->fun->getPost('estado'));

            $this->ccitas->updateEstado();

            http_response_code(200);
            echo json_encode(["message" => "Estado actualizado"]);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }

    //Obtener las citas programadas con un medico
    //
    //
    public function getByMedico(){
        if($this->fun->existGET(['id_medico'])){
            //Obtiene el id del medico y lo coloca en el modelo del medico
            $this->transitiva->setIdMedico($this->fun->getGet("id_medico"));
            //Obtiene el id del consultorio
            $this->transitiva->get();
            //Consulta de las citas del consultorio
            $this->ccitas->setIConsultorio($this->transitiva->getIdConsultorio());
            $data = $this->ccitas->getCitasConsultorio();
            //Manda la respuesta y los datos
            http_response_code(200);
            echo json_encode($data);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }

    //Obtiene las citas registradas de un paciente
    //
    //
    public function getCitasIdPaciente(){
        if($this->fun->existGET(['id_paciente'])){
            
            $this->ccitas->setIdPaciente($this->fun->getGet('id_paciente'));

            $data = $this->ccitas->getAllByPaciente();
            http_response_code(201);
            echo json_encode($data);
            exit;

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }

    //Obtiene los horarios disponibles
    //
    //
    public function getHorarios(){
        //Verifica que se manden todos los parametros
        if($this->fun->existGET(['id_consultorio','fecha'])){

            $id_consultorio = $this->fun->getGet('id_consultorio');
            $fecha = $this->fun->getGet('fecha');
            
            $horarios = $this->ccitas->getHorariosDisponibles($id_consultorio, $fecha);

            http_response_code(200); 
            echo json_encode($horarios); 
            exit;

        } else {
            http_response_code(400);
            echo json_encode(["message" => "Faltan parametros"]);
            exit;
        }
    }
}
