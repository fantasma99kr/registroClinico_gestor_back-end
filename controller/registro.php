<?php

    class Registro
    {

        private $fun;
        private $cpaciente;
        private $cmedico;
        private $consultorio;
        private $transitiva;

        public function __construct(){
            $this->fun = new Funciones();
            $this->cmedico = new MedicoModel();
            $this->cpaciente = new PacienteModel();
            $this->transitiva = new TransitivaConsultoriosModel();
            $this->consultorio = new ConsultorioModel();
        }

        //Registro del paciente
        public function registroPaciente(){
             //Verifica que se manden los parametros
            if($this->fun->existPOST(['correo','password','nombre','apellidos','telefono','fecha_nacimiento'])){

                //Obtiene los datos de los parametros
                $correo = $this->fun->getPost('correo');
                $this->cpaciente->setCorreo($correo);
                $this->cpaciente->setPassword(password_hash($this->fun->getPost('password'), PASSWORD_DEFAULT));
                $this->cpaciente->setNombre($this->fun->getPost('nombre'));
                $this->cpaciente->setApellidos($this->fun->getPost('apellidos'));
                $this->cpaciente->setNoTelefono($this->fun->getPost('telefono'));
                //Pasa la fecha al formato de la base de datos
                $this->cpaciente->setFechaNacimiento($this->fun->getPost('fecha_nacimiento'));

                //Verifica que el correo no este registrado
                if(!$this->cmedico->getByCorreo($correo) && !$this->cpaciente->getByCorreo($correo)){
                    //Realiza el registro
                    $this->cpaciente->newPaciente();
                    http_response_code(201);
                    echo json_encode(["message" => "ok"]);
                    exit;
                } else{
                    http_response_code(409); 
                    echo json_encode(["message" => "El correo ya esta registrado"]);
                    exit;
                }
            }else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
            
        }
        
        //Registro de medico
        public function registroMedico(){
            //Verifica que se manden los parametros
            if($this->fun->existPOST(['correo','password','nombre','apellidos'])){

                //Obtiene los datos de los parametros
                $correo = $this->fun->getPost('correo');
                
                $this->cmedico->setCorreo($correo);
                $this->cmedico->setPassword(password_hash($this->fun->getPost('password'), PASSWORD_DEFAULT));
                $this->cmedico->setNombre($this->fun->getPost('nombre'));
                $this->cmedico->setApellidos($this->fun->getPost('apellidos'));
                
                //Verifica que el correo no este registrado
                if(!$this->cmedico->getByCorreo($correo) && !$this->cpaciente->getByCorreo($correo)){
                    
                    if($this->cmedico->new()){

                        //Registra el consultorio del medico
                        $id_medico = $this->cmedico->getLastMedico();
                        $this->consultorio->new();
                        $id_consultorio = $this->consultorio->getLastConsultorio();
                        $this->transitiva->setIdMedico($id_medico);
                        $this->transitiva->setIdConsultorio($id_consultorio);
                        $this->transitiva->new();

                        //Mensaje de exito del registro
                        http_response_code(201);
                        echo json_encode(["message" => "Usuario registrado correctamente"]);
                        exit;
                    } else {
                        http_response_code(500); 
                        echo json_encode(["message" => "Error en el registro del paciente"]);
                        exit;
                    }
                } else{
                    http_response_code(409); 
                    echo json_encode(["message" => "El correo ya esta registrado"]);
                    exit;
                }
            }else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
            
        }

        
    }

?>