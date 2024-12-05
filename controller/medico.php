<?php

    class MedicoController{
        
        //Variables de instancia
        private $fun;
        private $medico;

        //Constructor
        //
        //
        public function __construct(){
            //Funciones generales
            $this->fun = new Funciones();
            //Objeto del modelo del medico
            $this->medico = new MedicoModel();
        }

        public function getinfo(){
            //Valida que se mande el id del medico
            if($this->fun->existGET(['id_medico'])){
                //Define la variable id medico en el objeto del modelo del medico
                $this->medico->setIdMedico($this->fun->getGet('id_medico'));
                //Obtiene los datos del medico
                echo $this->medico->get();
                http_response_code(200);
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        public function update(){
            //Valida que se mande el id del medico
            if($this->fun->existPOST(['id_medico', 'nombre', 'apellidos', 'cedula'])){
                //Se define el id del modelo del medico
                $this->medico->setIdMedico($this->fun->getPost('id_medico'));
                //Obtiene y define todos los datos que no sean los generales
                $this->medico->get();
                $this->medico->setNombre($this->fun->getPost('nombre'));
                $this->medico->setApellidos($this->fun->getPost('apellidos'));
                $this->medico->setCedula($this->fun->getPost('cedula'));
                $this->medico->update();
                http_response_code(200);
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        public function changePassword(){
            //Valida que se mande el id del medico
            if($this->fun->existPOST(['id_medico', 'password'])){
                //Se define el id del modelo del medico
                $this->medico->setIdMedico($this->fun->getPost('id_medico'));
                $this->medico->get();
                //Se sobrescribe la contraseña
                $this->medico->setPassword(password_hash($this->fun->getPost('password'), PASSWORD_DEFAULT));
                //Obtiene todos los datos para actualizar la contraseña
                $this->medico->update();
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