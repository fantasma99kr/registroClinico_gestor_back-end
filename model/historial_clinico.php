<?php

// Modelo del historial clínico
class HistorialClinico extends DataBase{
    // Variables de la tabla
    private $id_historial;
    private $id_paciente;
    private $titulo;
    private $documento;
    private $imagen;
    private $descripcion;

    // Constructor
    public function __construct() {
        //Se inicializa el constructor de la base de datos
        parent::__construct();
        //Inicializacion de las variables
        $this->id_historial = '';
        $this->id_paciente = '';
        $this->titulo = '';
        $this->documento = '';
        $this->imagen = '';
        $this->descripcion = '';
    }

    //Obtiene los datos del historial por id
    public function getByID(){
        try {
            $query = 'SELECT * FROM historial_clinico WHERE id_historial = :id_historial';
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'id_historial' => $this->id_historial
            ]);
            
            //Obtiene todos los datos
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            //Verifica que el resultado no este vacio antes de settear los datos
            if($result){
                $this->setIdPaciente($result['id_paciente']); 
                $this->setTitulo($result['titulo']); 
                $this->setDocumento($result['documento']); 
                $this->setImagen($result['imagen']); 
                $this->setDescripcion($result['descripcion']);
            } else {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error al obtener los datos"]); 
            error_log("[MODEL:HistorialClinico] Error en la función new: " . $e->getMessage());
            die;
        }
    }

    //Obtiene los datos del historial de un paciente
    public function getAllPaciente(){
        try {
            $query = 'SELECT * FROM historial_clinico WHERE id_paciente = :id_paciente';
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'id_paciente' => $this->id_paciente
            ]);
            
            //Obtiene todos los datos
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error al obtener los datos"]); 
            error_log("[MODEL:HistorialClinico] Error en la función new: " . $e->getMessage());
            die;
        }
    }

    //Guardar nuevo registro
    public function save(){
        try{
            $query = "INSERT INTO historial_clinico (id_paciente, titulo, documento, imagen, descripcion) 
                      VALUES (:id_paciente, :titulo, :documento, :imagen, :descripcion)";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_paciente' => $this->id_paciente,
                'titulo' => $this->titulo,
                'documento' => $this->documento,
                'imagen' => $this->imagen,
                'descripcion' => $this->descripcion
            ]);

            return true;
        } catch(PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Consultorio] Error en la función save: " . $e->getMessage());
            die;
        }
    }

    //Obtener todos los registros
    public function getAll() {
        try {
            $query = 'SELECT *  FROM historial_clinico';
            $stmt = $this->con->prepare($query);
            $stmt->execute([]);
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Consultorio] Error en la función getAll: " . $e->getMessage());
            die;
        }
    }

    // Actualizar información de un historial clínico
    public function update() {
        try {
            $query = "UPDATE historial_clinico SET 
                      id_paciente = :id_paciente, 
                      titulo = :titulo, 
                      documento = :documento, 
                      imagen = :imagen, 
                      descripcion = :descripcion
                      WHERE id_historial = :id_historial";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_historial' => $this->id_historial,
                'id_paciente' => $this->id_paciente,
                'titulo' => $this->titulo,
                'documento' => $this->documento,
                'imagen' => $this->imagen,
                'descripcion' => $this->descripcion
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:HistorialClinico] Error en la función update: " . $e->getMessage());
            die;
        }
    }

    //Funcion para eliminar el registro del historial de la base de datos
    public function delete(){
        try {
            $query = "DELETE FROM historial_clinico WHERE id_historial = :id_historial";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_historial' => $this->id_historial
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:HistorialClinico] Error en la función update: " . $e->getMessage());
            die;
        }
    }

    // Setters
    public function setIdHistorial($id_historial) { $this->id_historial = $id_historial; }
    public function setIdPaciente($id_paciente) { $this->id_paciente = $id_paciente; }
    public function setTitulo($titulo) { $this->titulo = $titulo; }
    public function setDocumento($documento) { $this->documento = $documento; }
    public function setImagen($imagen) { $this->imagen = $imagen; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    // Getters
    public function getIdHistorial() { return $this->id_historial; }
    public function getIdPaciente() { return $this->id_paciente; }
    public function getTitulo() { return $this->titulo; }
    public function getDocumento() { return $this->documento; }
    public function getImagen() { return $this->imagen; }
    public function getDescripcion() { return $this->descripcion; }
}

?>
