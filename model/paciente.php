<?php

class PacienteModel extends DataBase {
    // Variables de la tabla
    private $id_paciente;
    private $nombre;
    private $apellidos;
    private $no_telefono;
    private $correo;
    private $password;
    private $fecha_nacimiento;

    // Constructor
    public function __construct() {
        // Se inicializa el constructor de la base de datos
        parent::__construct();
        $this->id_paciente = '';
        $this->nombre = '';
        $this->apellidos = '';
        $this->no_telefono = '';
        $this->correo = '';
        $this->password = '';
        $this->fecha_nacimiento = '';
    }

    // Obtener un paciente por ID
    public function get() {
        try {
            $query = 'SELECT id_paciente, nombre, apellidos, no_telefono, correo, password, fecha_nacimiento 
                      FROM pacientes WHERE id_paciente = :id_paciente';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_paciente' => $this->getIdPaciente()]);

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id_paciente = $data['id_paciente'];
                $this->nombre = $data['nombre'];
                $this->apellidos = $data['apellidos'];
                $this->no_telefono = $data['no_telefono'];
                $this->correo = $data['correo'];
                $this->password = $data['password'];
                $this->fecha_nacimiento = $data['fecha_nacimiento'];

                return json_encode($data);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Paciente] Error en la función get: " . $e->getMessage());
            die;
        }
    }

    // Obtener un paciente por correo
    public function getByCorreo($correo) {
        try {
            $query = 'SELECT id_paciente, nombre, apellidos, no_telefono, correo, password, fecha_nacimiento 
                      FROM pacientes WHERE correo = :correo';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['correo' => $correo]);

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id_paciente = $data['id_paciente'];
                $this->nombre = $data['nombre'];
                $this->apellidos = $data['apellidos'];
                $this->no_telefono = $data['no_telefono'];
                $this->correo = $data['correo'];
                $this->password = $data['password'];
                $this->fecha_nacimiento = $data['fecha_nacimiento'];

                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Paciente] Error en la función getByCorreo: " . $e->getMessage());
            die;
        }
    }

    // Crear un nuevo registro de paciente
    public function newPaciente() {
        try {
            $query = "INSERT INTO pacientes (nombre, apellidos, no_telefono, correo, password, fecha_nacimiento) 
                      VALUES (:nombre, :apellidos, :no_telefono, :correo, :password, :fecha_nacimiento)";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'no_telefono' => $this->no_telefono,
                'correo' => $this->correo,
                'password' => $this->password,
                'fecha_nacimiento' => $this->fecha_nacimiento
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Paciente] Error en la función new: " . $e->getMessage());
            die;
        }
    }

    // Actualizar información de un paciente
    public function update() {
        try {
            $query = "UPDATE pacientes SET 
                      nombre = :nombre, 
                      apellidos = :apellidos, 
                      no_telefono = :no_telefono, 
                      correo = :correo, 
                      password = :password, 
                      fecha_nacimiento = :fecha_nacimiento
                      WHERE id_paciente = :id_paciente";
            $stmt = $this->con->prepare($query);
            $stmt->execute([
                'id_paciente' => $this->id_paciente,
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'no_telefono' => $this->no_telefono,
                'correo' => $this->correo,
                'password' => $this->password,
                'fecha_nacimiento' => $this->fecha_nacimiento
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Paciente] Error en la función update: " . $e->getMessage());
            die;
        }
    }

    // Setters y Getters
    public function setIdPaciente($id_paciente) { $this->id_paciente = $id_paciente; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setNoTelefono($no_telefono) { $this->no_telefono = $no_telefono; }
    public function setCorreo($correo) { $this->correo = $correo; }
    public function setPassword($password) { $this->password = $password; }
    public function setFechaNacimiento($fecha_nacimiento) { $this->fecha_nacimiento = $fecha_nacimiento; }

    public function getIdPaciente() { return $this->id_paciente; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getNoTelefono() { return $this->no_telefono; }
    public function getCorreo() { return $this->correo; }
    public function getPassword() { return $this->password; }
    public function getFechaNacimiento() { return $this->fecha_nacimiento; }
}

?>
