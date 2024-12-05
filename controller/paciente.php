<?php

    class ControllerPaciente{
        
        //Variables de instancia
        private $fun;
        private $cpaciente;

        //Constructor
        //
        //
        public function __construct(){
            //Funciones generales
            $this->fun = new Funciones();
            //Objeto del modelo de paciente
            $this->cpaciente = new PacienteModel();
        }

        
        //Obtiene los datos del paciente
        //
        //
        public function get(){
            if($this->fun->existGET(['id_paciente'])){
                //Define el valor de id paciente en el objeto del modelo del paciente
                $this->cpaciente->setIdPaciente($this->fun->getGet('id_paciente'));
                //Obtiene los datos del paciente
                echo $this->cpaciente->get();
                http_response_code(200);
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        //Actualizar contraseña
        //
        //
        public function updatePass(){
            if($this->fun->existPOST(['id_paciente','password'])){
                $this->cpaciente->setIdPaciente($this->fun->getPost('id_paciente'));
                //Busca los datos del paciente y los setea
                $this->cpaciente->get();
                //Hashea la nueva contraseña
                $this->cpaciente->setPassword(password_hash($this->fun->getPost('password'), PASSWORD_DEFAULT));
                //Se actualizan los datos
                http_response_code(200);
                $this->cpaciente->update();
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }


        //Actualizar los datos generales del paciente
        //
        //
        public function update(){
            //
            if($this->fun->existPOST(['id_paciente','nombre','apellidos','telefono','fecha_nacimiento'])){
                $this->cpaciente->setIdPaciente($this->fun->getPost('id_paciente'));
                //Busca los datos del paciente y los setea
                $this->cpaciente->get();
                //Obtiene los nuevos datos de los parametros
                $this->cpaciente->setNombre($this->fun->getPost('nombre'));
                $this->cpaciente->setApellidos($this->fun->getPost('apellidos'));
                $this->cpaciente->setNoTelefono($this->fun->getPost('telefono'));
                //Pasa la fecha al formato de la base de datos
                $this->cpaciente->setFechaNacimiento($this->fun->getPost('fecha_nacimiento'));

                //Actualiza los datos 
                $this->cpaciente->update();
                http_response_code(200);
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }
    }
?>