<?php


    class recetasController{
        
        private $fun;
        private $recetas;

        public function __controler(){
            $this->fun = new Funciones();
            $this->recetas = new RecetaModel();
        }

        public function newReceta(){
            //Verificar si faltan parametros
            error_log("new");
            if($this->fun->existPOST(['id_paciente','id_medico' ,'id_consultorio', 'edad', 'estatura', 'peso', 'diagnostico', 'fecha', 'alergias', 'temperatura', 'tratamiento', 't_a'])){
                $this->recetas->setIdMedico($this->fun->getPost('id_medico'));
                $this->recetas->setIdConsultorio($this->fun->getPost('id_consultorio'));
                $this->recetas->setIdPaciente($this->fun->getPost('id_paciente'));
                $this->recetas->setEdad($this->fun->getPost('edad'));
                $this->recetas->setEstatura($this->fun->getPost('estatura'));
                $this->recetas->setPeso($this->fun->getPost('peso'));
                $this->recetas->setDiagnostico($this->fun->getPost('diagnostico'));
                $this->recetas->setFecha($this->fun->getPost('fecha'));
                $this->recetas->setAlergias($this->fun->getPost('alergias'));
                $this->recetas->setTemperatura($this->fun->getPost('temperatura'));
                $this->recetas->setTratamiento($this->fun->getPost('tratamiento'));
                $this->recetas->setTA($this->fun->getPost('t_a'));

                $this->recetas->new();

                http_response_code(201);
                echo json_encode(["message" => "Registro realizado con exito"]);  
                exit;

            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        //Obtener las recetas de un paciente
        //
        //
        public function getRePaciente(){
            //Verificar si faltan parametros
            if($this->fun->existPOST(['id_paciente'])){
                
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }


        //Obtener las recetas de prescritas de un medico
        //
        //
        public function getReMedico(){
            //Verificar si faltan parametros
            if($this->fun->existPost(['id_paciente'])){
                
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }
    }


?>