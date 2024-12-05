<?php

class TransitivaConsultoriosModel extends DataBase{

    // Variables de la tabla
    private $id_medico_consultorio;
    private $id_medico;
    private $id_consultorio;

    // Constructor
    public function __construct() {
        //Se inicializa el constructor de la base de datos
        parent::__construct();
        //Variables de la tabla transitiva de los consultorios
        $this->id_medico_consultorio = '';
        $this->id_medico = '';
        $this->id_consultorio = '';
    }

    // Obiene el id del consultorio por el id del medico
    //
    //
    public function get() {
        try {
            $query = 'SELECT * FROM transitiva_consultorios WHERE id_medico = :id_medico';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_medico' => $this->id_medico]);
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->setIdMedico($data['id_medico']);
                
            $this->setIdConsultorio($data['id_consultorio']);


        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:TransitivaConsultorios] Error en la función get: " . $e->getMessage());
            die;
        }
    }

    // Crear un nuevo registro
    public function new() {
        try {
            $query = "INSERT INTO transitiva_consultorios (id_medico, id_consultorio) 
                      VALUES (:id_medico, :id_consultorio)";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_medico' => $this->id_medico,
                'id_consultorio' => $this->id_consultorio
            ]);

            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Error por restricción UNIQUE
                return "La relación entre el médico y el consultorio ya existe.";
            }
            http_response_code(500);
            error_log("[MODEL:TransitivaConsultorios] Error en la función new: " . $e->getMessage());
            die;
        }
    }

    // Eliminar un registro por ID
    public function delete($id_medico_consultorio) {
        try {
            $query = 'DELETE FROM transitiva_consultorios WHERE id_medico_consultorio = :id_medico_consultorio';
            $stmt = $this->con->prepare($query);

            $stmt->execute(['id_medico_consultorio' => $id_medico_consultorio]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:TransitivaConsultorios] Error en la función delete: " . $e->getMessage());
            die;
        }
    }

    // Verificar si la relación entre médico y consultorio ya existe
    public function exists($id_medico, $id_consultorio) {
        try {
            $query = "SELECT * FROM transitiva_consultorios WHERE id_medico = :id_medico AND id_consultorio = :id_consultorio";
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'id_medico' => $id_medico,
                'id_consultorio' => $id_consultorio
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:TransitivaConsultorios] Error en la función exists: " . $e->getMessage());
            die;
        }
    }

    // Setters y Getters
    public function setIdMedicoConsultorio($id_medico_consultorio) { $this->id_medico_consultorio = $id_medico_consultorio; }
    public function setIdMedico($id_medico) { $this->id_medico = $id_medico; }
    public function setIdConsultorio($id_consultorio) { $this->id_consultorio = $id_consultorio; }

    public function getIdMedicoConsultorio() { return $this->id_medico_consultorio; }
    public function getIdMedico() { return $this->id_medico; }
    public function getIdConsultorio() { return $this->id_consultorio; }
}

?>
