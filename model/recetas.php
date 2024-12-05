<?php

class RecetaModel extends DataBase {

    // Variables de la tabla
    private $id_receta;
    private $id_medico;
    private $id_consultorio;
    private $id_paciente;
    private $edad;
    private $estatura;
    private $peso;
    private $diagnostico;
    private $fecha;
    private $alergias;
    private $temperatura;
    private $tratamiento;
    private $t_a;

    // Constructor
    //
    //
    public function __construct() {
        // Se inicializa el constructor de la base de datos
        parent::__construct();
        // Variables
        $this->id_receta = '';
        $this->id_medico = '';
        $this->id_consultorio = '';
        $this->id_paciente = '';
        $this->edad = '';
        $this->estatura = '';
        $this->peso = '';
        $this->diagnostico = '';
        $this->fecha = '';
        $this->alergias = '';
        $this->temperatura = '';
        $this->tratamiento = '';
        $this->t_a = '';
    }

    // Obtener una receta por ID
    //
    //
    public function get() {
        try {
            $query = 'SELECT * FROM recetas WHERE id_receta = :id_receta';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_receta' => $this->id_receta]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Receta] Error en la función get: " . $e->getMessage());
            exit;
        }
    }

    // Obtener las recetas de un medico
    //
    //
    public function getRecetasMedico(){
        try {
            $query = 'SELECT recetas.id_receta, recetas.id_medico, recetas.id_consultorio, recetas.id_paciente, pacientes.nombre AS nombre_paciente, pacientes.apellidos AS apellidos_paciente, recetas.edad, recetas.estatura, recetas.peso, recetas.diagnostico, recetas.fecha, recetas.alergias, recetas.temperatura, recetas.tratamiento, recetas.t_a
                        FROM recetas
                        JOIN pacientes ON recetas.id_paciente = pacientes.id_paciente
                        WHERE recetas.id_medico = :id_medico;';
            
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_medico' => $this->id_medico]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Receta] Error en la función getRecetasConsultorio: " . $e->getMessage());
            exit;
        }
    }

    // Obtener todas las recetas de un paciente
    //
    //
    public function getAllByPaciente() {
        try {
            $query = "SELECT recetas.*, medicos.nombre AS nombre_medico, medicos.apellidos AS apellidos_medico, consultorios.nombre AS nombre_consultorio
                      FROM recetas
                      LEFT JOIN medicos ON recetas.id_medico = medicos.id_medico
                      LEFT JOIN consultorios ON recetas.id_consultorio = consultorios.id_consultorio
                      WHERE recetas.id_paciente = :id_paciente";

            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_paciente' => $this->id_paciente]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Receta] Error en la función getAllByPaciente: " . $e->getMessage());
            exit;
        }
    }

    // Crear una nueva receta
    //
    //
    public function new() {
        try {
            $query = "INSERT INTO recetas (id_medico, id_consultorio, id_paciente, edad, estatura, peso, diagnostico, fecha, alergias, temperatura, tratamiento, t_a) 
                      VALUES (:id_medico, :id_consultorio, :id_paciente, :edad, :estatura, :peso, :diagnostico, :fecha, :alergias, :temperatura, :tratamiento, :t_a)";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_medico' => $this->id_medico,
                'id_consultorio' => $this->id_consultorio,
                'id_paciente' => $this->id_paciente,
                'edad' => $this->edad,
                'estatura' => $this->estatura,
                'peso' => $this->peso,
                'diagnostico' => $this->diagnostico,
                'fecha' => $this->fecha,
                'alergias' => $this->alergias,
                'temperatura' => $this->temperatura,
                'tratamiento' => $this->tratamiento,
                't_a' => $this->t_a
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Receta] Error en la función new: " . $e->getMessage());
            exit;
        }
    }

    // Actualizar una receta existente
    //
    //
    public function update() {
        try {
            $query = "UPDATE recetas SET 
                      id_medico = :id_medico, id_consultorio = :id_consultorio, id_paciente = :id_paciente, edad = :edad, estatura = :estatura, peso = :peso, 
                      diagnostico = :diagnostico, fecha = :fecha, alergias = :alergias, temperatura = :temperatura, tratamiento = :tratamiento, t_a = :t_a
                      WHERE id_receta = :id_receta";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_receta' => $this->id_receta,
                'id_medico' => $this->id_medico,
                'id_consultorio' => $this->id_consultorio,
                'id_paciente' => $this->id_paciente,
                'edad' => $this->edad,
                'estatura' => $this->estatura,
                'peso' => $this->peso,
                'diagnostico' => $this->diagnostico,
                'fecha' => $this->fecha,
                'alergias' => $this->alergias,
                'temperatura' => $this->temperatura,
                'tratamiento' => $this->tratamiento,
                't_a' => $this->t_a
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Receta] Error en la función update: " . $e->getMessage());
            exit;
        }
    }

    // Setters y Getters para cada atributo
    //
    //
    public function setIdReceta($id_receta) { $this->id_receta = $id_receta; }
    public function setIdMedico($id_medico) { $this->id_medico = $id_medico; }
    public function setIdConsultorio($id_consultorio) { $this->id_consultorio = $id_consultorio; }
    public function setIdPaciente($id_paciente) { $this->id_paciente = $id_paciente; }
    public function setEdad($edad) { $this->edad = $edad; }
    public function setEstatura($estatura) { $this->estatura = $estatura; }
    public function setPeso($peso) { $this->peso = $peso; }
    public function setDiagnostico($diagnostico) { $this->diagnostico = $diagnostico; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setAlergias($alergias) { $this->alergias = $alergias; }
    public function setTemperatura($temperatura) { $this->temperatura = $temperatura; }
    public function setTratamiento($tratamiento) { $this->tratamiento = $tratamiento; }
    public function setTA($t_a) { $this->t_a = $t_a; }

    public function getIdReceta() { return $this->id_receta; }
    public function getIdMedico() { return $this->id_medico; }
    public function getIdConsultorio() { return $this->id_consultorio; }
    public function getIdPaciente() { return $this->id_paciente; }
    public function getEdad() { return $this->edad; }
    public function getEstatura() { return $this->estatura; }
    public function getPeso() { return $this->peso; }
    public function getDiagnostico() { return $this->diagnostico; }
    public function getFecha() { return $this->fecha; }
    public function getAlergias() { return $this->alergias; }
    public function getTemperatura() { return $this->temperatura; }
    public function getTratamiento() { return $this->tratamiento; }
    public function getTA() { return $this->t_a; }
}

?>
