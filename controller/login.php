<?php

    class Login
    {

        private $fun;
        private $cpaciente;
        private $cmedico;

        //Constructor
        public function __construct(){
            $this->fun = new Funciones();
            $this->cmedico = new MedicoModel();
            $this->cpaciente = new PacienteModel();
        }
        
        //Funcion para la validacion del login
        public function validar(){
            if($this->fun->existPOST(['correo','password'])){
                $correo = $this->fun->getPost('correo');
                $password = $this->fun->getPost('password');

                if($this->cmedico->getByCorreo($correo)){
                    $this->cmedico->setCorreo($correo);
                    if(password_verify($password,$this->cmedico->getPassword())){//Si la contraseña es correcta manda un codigo 201 y el tipo de usuario
                        http_response_code(201);
                        echo json_encode(["message" => "Inicio de sesion correcto", "user_type" => "medico", "id_user" => $this->cmedico->getIdMedico()]);
                    } else {
                        http_response_code(401);
                        echo json_encode(["message" => "Contraseña incorrecta"]);
                        exit;
                    }
                } else if($this->cpaciente->getByCorreo($correo)){
                    $this->cpaciente->setCorreo($correo);
                    if(password_verify($password,$this->cpaciente->getPassword())){//Si la contraseña es correcta manda un codigo 201 y el tipo de usuario
                        http_response_code(201);
                        echo json_encode(["message" => "Inicio de sesion correcto", "user_type" => "paciente", "id_user" => $this->cpaciente->getIdPaciente()]);
                        exit;
                    } else {
                        http_response_code(401);
                        echo json_encode(["message" => "Contraseña incorrecta"]);
                        exit;    
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Correo no registrado"]);
                    exit;                    
                }

            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

    
    }

?>