<?php

    class ConsultorioModel extends DataBase{

        // Variables de la tabla
        private $id_consultorio;
        private $nombre;
        private $direccion;
        private $coordenadas;
        private $abre_h;
        private $cierra_h;
        private $dias;
        private $telefono;
        private $correo;
        private $especialidad;
        private $informacion;
        private $latitud;
        private $longitud;
        private $km;
        private $last_consultorio;

        //Constructor
        //
        //
        public function __construct() {
            //Se inicializa el constructor de la base de datos
            parent::__construct();
            //Variables de los consultorios
            $this->id_consultorio = "";
            $this->nombre = '';
            $this->direccion = '';
            $this->coordenadas = '0, 0';
            $this->abre_h = '';
            $this->cierra_h = '';
            $this->dias = '';
            $this->telefono = '';
            $this->correo = '';
            $this->especialidad = '';
            $this->latitud = '0';
            $this->longitud = '0';
            $this->last_consultorio = '';
        }

    //Obtener los datos del medico por el id del consultorio
    //
    //
    public function getConsultorioByIdMedico($id_medico) {
        $id_medico = (int)$id_medico; // Cast a entero
    
        try {
            $query = "SELECT consultorios.id_consultorio, consultorios.nombre, consultorios.direccion, consultorios.coordenadas, consultorios.abre_h, consultorios.cierra_h, consultorios.dias, consultorios.telefono, consultorios.correo, consultorios.especialidad, consultorios.informacion
                      FROM consultorios
                      INNER JOIN transitiva_consultorios ON consultorios.id_consultorio = transitiva_consultorios.id_consultorio
                      WHERE transitiva_consultorios.id_medico = :id_medico";
    
            $stmt = $this->con->prepare($query);
            $stmt->bindParam(':id_medico', $id_medico, PDO::PARAM_INT);
    
            // Depuración para ver antes de ejecutar la consulta
            $stmt->execute();
            error_log("Consulta ejecutada, rowCount: " . $stmt->rowCount());
    
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Datos obtenidos: " . json_encode($data));
    
            if ($data) {
                return json_encode($data);
            } else {
                http_response_code(404);
                return json_encode(["message" => "Consultorio not found"]);
            }
    
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Consultorio] Error en la función getConsultorioByIdMedico: " . $e->getMessage());
            return json_encode(["message" => "Internal server error"]);
        }
    }
    
    
        
        

        //Obtener todos los consultorios y quita los consultorios que aun no proporcionan las coordenadas del consultorio
        //En la conulta para evitar que los medicos que no tengan registrado el consultorio en el sistema al no contar con esa opcion de edicion de datos del consultorio al estar limitado a
        //los 5 consultorios al registrarse los consultorios de los medicos se registran al inicio con latitud 0 y longitud 0
        //para cuando se consulten para mostrarlos no se muestren en el mapa al filtrarlos en la consulta
        //
        //
        public function getAll() {
            try {
                
                $query = "SELECT id_consultorio, nombre, direccion, ST_X(coordenadas) AS latitud, ST_Y(coordenadas) AS longitud, abre_h, cierra_h, dias, telefono, correo, especialidad, informacion FROM consultorios WHERE ST_AsText(coordenadas) != 'POINT(0 0)'";
                $stmt = $this->con->prepare($query);
                $stmt->execute([]);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                http_response_code(500);
                error_log("[MODEL:Consultorio] Error en la función getAll: " . $e->getMessage());
                die;
            }
        }


        //Crear un nuevo consultorio
        //
        //
        public function new() {
            try {
                $query = "INSERT INTO consultorios (nombre, direccion, coordenadas, abre_h, cierra_h, dias, telefono, correo, especialidad, informacion) 
                        VALUES (:nombre, :direccion, POINT(:latitud, :longitud), :abre_h, :cierra_h, :dias, :telefono, :correo, :especialidad, :informacion);";
                $stmt = $this->con->prepare($query);

                $stmt->execute([
                    'nombre' => $this->nombre,
                    'direccion' => $this->direccion,
                    'latitud' => $this->latitud,
                    'longitud' => $this->longitud,
                    'abre_h' => $this->abre_h,
                    'cierra_h' => $this->cierra_h,
                    'dias' => $this->dias,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                    'especialidad' => $this->especialidad,
                    'informacion' => $this->informacion
                ]);

                $this->setLastConsultorio($this->con->lastInsertId());

                return true;
            } catch (PDOException $e) {
                http_response_code(500);
                error_log("[MODEL:Consultorio] Error en la función new: " . $e->getMessage());
                die;
            }
        }

        //Actualizar un consultorio existente
        //
        //
        public function update() {
            try {
                $query = "UPDATE consultorios SET 
                        nombre = :nombre, direccion = :direccion, coordenadas = POINT(:latitud, :longitud), abre_h = :abre_h, cierra_h = :cierra_h, 
                        dias = :dias, telefono = :telefono, correo = :correo, especialidad = :especialidad, informacion = :informacion 
                        WHERE id_consultorio = :id_consultorio";
                $stmt = $this->con->prepare($query);

                $stmt->execute([
                    'id_consultorio' => $this->id_consultorio,
                    'nombre' => $this->nombre,
                    'direccion' => $this->direccion,
                    'latitud' => $this->latitud,
                    'longitud' => $this->longitud,
                    'abre_h' => $this->abre_h,
                    'cierra_h' => $this->cierra_h,
                    'dias' => $this->dias,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                    'especialidad' => $this->especialidad,
                    'informacion' => $this->informacion
                ]);

                return true;
            } catch (PDOException $e) {
                http_response_code(500);
                error_log("[MODEL:Consultorio] Error en la función update: " . $e->getMessage());
                die;
            }
        }

        //Setters y Getters para cada atributo
        public function setIdConsultorio($id_consultorio) { $this->id_consultorio = $id_consultorio; }
        public function setNombre($nombre) { $this->nombre = $nombre; }
        public function setDireccion($direccion) { $this->direccion = $direccion; }
        public function setCoordenadas($coordenadas) { $this->coordenadas = $this->latitud . ", " . $this->longitud; }
        public function setAbreH($abre_h) { $this->abre_h = $abre_h; }
        public function setCierraH($cierra_h) { $this->cierra_h = $cierra_h; }
        public function setDias($dias) { $this->dias = $dias; }
        public function setTelefono($telefono) { $this->telefono = $telefono; }
        public function setCorreo($correo) { $this->correo = $correo; }
        public function setEspecialidad($especialidad) { $this->especialidad = $especialidad; }
        public function setInformacion($informacion) { $this->informacion = $informacion; }
        public function setLatitud($latitud){ $this->latitud = $latitud; }
        public function setLongitud($longitud){ $this->longitud = $longitud; }
        public function setLastConsultorio($last_consultorio) { $this->last_consultorio = $last_consultorio;}


        public function getIdConsultorio() { return $this->id_consultorio; }
        public function getNombre() { return $this->nombre; }
        public function getDireccion() { return $this->direccion; }
        public function getCoordenadas() { return $this->coordenadas; }
        public function getAbreH() { return $this->abre_h; }
        public function getCierraH() { return $this->cierra_h; }
        public function getDias() { return $this->dias; }
        public function getTelefono() { return $this->telefono; }
        public function getCorreo() { return $this->correo; }
        public function getEspecialidad() { return $this->especialidad; }
        public function getInformacion() { return $this->informacion; }
        public function getLastConsultorio(){ return $this->last_consultorio; }
    }

?>
