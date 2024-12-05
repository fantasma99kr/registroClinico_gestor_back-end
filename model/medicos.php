<?php

//Modelo de los medicos
class MedicoModel extends DataBase {
    // Variables de la tabla
    private $id_medico;
    private $nombre;
    private $apellidos;
    private $correo;
    private $password;
    private $cedula;
    private $last_medico;

    // Constructor
    public function __construct() {
        // Se inicializa el constructor de la base de datos
        parent::__construct();
        $this->id_medico = '';
        $this->nombre = '';
        $this->apellidos = '';
        $this->correo = '';
        $this->password = '';
        $this->cedula = '';
        $this->last_medico = '';
    }

    // Obtener un médico por ID
    public function get() {
        try {
            $query = 'SELECT * FROM medicos WHERE id_medico = :id_medico';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_medico' => $this->getIdMedico()]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->nombre = $data['nombre'];
                $this->apellidos = $data['apellidos'];
                $this->correo = $data['correo'];
                $this->password = $data['password'];
                $this->cedula = $data['cedula'];
                return json_encode($data);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Medico] Error en la función get: " . $e->getMessage());
            die;
        }
    }

    // Obtener los datos del medico por el id del consultorio
    public function getByConsulId($id_consultorio) {
        try {
            $query = "SELECT medicos.id_medico, medicos.nombre, medicos.apellidos, medicos.correo, medicos.cedula 
                      FROM medicos 
                      INNER JOIN transitiva_consultorios ON medicos.id_medico = transitiva_consultorios.id_medico 
                      WHERE transitiva_consultorios.id_consultorio = :id_consultorio;";
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_consultorio' => $id_consultorio]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->nombre = $data['nombre'];
                $this->apellidos = $data['apellidos'];
                $this->correo = $data['correo'];
                $this->cedula = $data['cedula'];
                return json_encode($data);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Medico] Error en la función getByConsulId: " . $e->getMessage());
            die;
        }
    }

    // Obtener un médico por correo
    public function getByCorreo($correo) {
        try {
            $query = 'SELECT * FROM medicos WHERE correo = :correo';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['correo' => $correo]);
            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->setIdMedico($data['id_medico']);
                $this->setNombre($data['nombre']);
                $this->setApellidos($data['apellidos']);
                $this->setCorreo($data['correo']);
                $this->setPassword($data['password']);
                $this->setCedula($data['cedula']);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Medico] Error en la función getByCorreo: " . $e->getMessage());
            die;
        }
    }

    // Crear un nuevo registro de médico
    public function new() {
        try {
            $query = "INSERT INTO medicos (nombre, apellidos, correo, password) 
                      VALUES (:nombre, :apellidos, :correo, :password)";
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'correo' => $this->correo,
                'password' => $this->password
            ]);
            //Obtiene el id del medico que se registro
            $this->setLastMedico($this->con->lastInsertId());

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Medico] Error en la función new: " . $e->getMessage());
            die;
        }
    }

    // Actualizar información de un médico
    public function update() {
        try {
            $query = "UPDATE medicos SET 
                      nombre = :nombre, 
                      apellidos = :apellidos, 
                      correo = :correo, 
                      password = :password, 
                      cedula = :cedula
                      WHERE id_medico = :id_medico";
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'id_medico' => $this->id_medico,
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'correo' => $this->correo,
                'password' => $this->password,
                'cedula' => $this->cedula
            ]);
            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Medico] Error en la función update: " . $e->getMessage());
            die;
        }
    }

    // Setters
    public function setIdMedico($id_medico) { $this->id_medico = $id_medico; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setCorreo($correo) { $this->correo = $correo; }
    public function setPassword($password) { $this->password = $password; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function setLastMedico($last_medico) { $this->last_medico = $last_medico; }
    // Getters
    public function getIdMedico() { return $this->id_medico; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getCorreo() { return $this->correo; }
    public function getPassword() { return $this->password; }
    public function getCedula() { return $this->cedula; }
    public function getLastMedico() { return $this->last_medico; }
}

?>
