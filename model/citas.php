<?php

class CitaModel extends DataBase {

    //Variables de la tabla
    private $id_cita;
    private $id_paciente;
    private $id_consultorio;
    private $permiso_historial;
    private $horario;
    private $fecha;
    private $datos_adicionales;
    private $estado;
    private $tipo_cita;

    //Constructor
    //
    //
    public function __construct() {
        //Se inicializa el constructor de la base de datos
        parent::__construct();
        //Variables
        $this->id_cita = '';
        $this->id_consultorio = '';
        $this->id_paciente = '';
        $this->permiso_historial = 'no';
        $this->horario = '';
        $this->fecha = '';
        $this->datos_adicionales = '';
        $this->estado = 'espera';
        $this->tipo_cita = 'general';
    }

    //Obtener una cita por ID
    //
    //
    public function get() {
        try {
            $query = 'SELECT * FROM citas WHERE id_cita = :id_cita';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_cita' => $this->id_cita]);

            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función get: " . $e->getMessage());
            exit;
        }
    }

    //Obtener una cita por ID
    //
    //
    public function getCitasConsultorio() {
        try {
            $query = 'SELECT citas.id_cita, citas.id_paciente, citas.id_consultorio, citas.permiso_historial, citas.horario, citas.fecha, citas.datos_adicionales, citas.estado, citas.tipo_cita, pacientes.nombre AS nombre_paciente, pacientes.apellidos AS apellidos_paciente
                        FROM citas
                        INNER JOIN pacientes ON citas.id_paciente = pacientes.id_paciente
                        WHERE citas.id_consultorio = :id_consultorio;';

            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_consultorio' => $this->id_consultorio]);

            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función get: " . $e->getMessage());
            exit;
        }
    }

    // Obtener todas las citas de un paciente
    //
    //
    public function getAllByPaciente() {
        try {
            $query = "SELECT citas.id_cita, citas.id_paciente, citas.id_consultorio, consultorios.nombre 
            AS nombre_consultorio, citas.permiso_historial, citas.horario, citas.fecha, citas.datos_adicionales, citas.estado, citas.tipo_cita 
            FROM citas
            LEFT JOIN consultorios ON citas.id_consultorio = consultorios.id_consultorio 
            WHERE citas.id_paciente = :id_paciente";

            $stmt = $this->con->prepare($query);
            
            $stmt->execute(['id_paciente' => $this->id_paciente]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función getAllByPaciente: " . $e->getMessage());
            exit;
        }
    }

    //Crear una nueva cita
    //
    //
    public function new() {
        try {
            $query = "INSERT INTO citas (id_paciente, id_consultorio, permiso_historial, horario, fecha, datos_adicionales, estado, tipo_cita) 
                      VALUES (:id_paciente, :id_consultorio, :permiso_historial, :horario, :fecha, :datos_adicionales, :estado, :tipo_cita)";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_paciente' => $this->id_paciente,
                'id_consultorio' => $this->id_consultorio,
                'permiso_historial' => $this->permiso_historial,
                'horario' => $this->horario,
                'fecha' => $this->fecha,
                'datos_adicionales' => $this->datos_adicionales,
                'estado' => $this->estado,
                'tipo_cita' => $this->tipo_cita
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función new: " . $e->getMessage());
            exit;
        }
    }

    //Actualizar una cita existente
    //
    //
    public function update() {
        try {
            $query = "UPDATE citas SET 
                      id_paciente = :id_paciente, id_consultorio = :id_consultorio, permiso_historial = :permiso_historial, 
                      horario = :horario, fecha = :fecha, datos_adicionales = :datos_adicionales, estado = :estado, tipo_cita = :tipo_cita
                      WHERE id_cita = :id_cita";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_cita' => $this->id_cita,
                'id_paciente' => $this->id_paciente,
                'id_consultorio' => $this->id_consultorio,
                'permiso_historial' => $this->permiso_historial,
                'horario' => $this->horario,
                'fecha' => $this->fecha,
                'datos_adicionales' => $this->datos_adicionales,
                'estado' => $this->estado,
                'tipo_cita' => $this->tipo_cita
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función update: " . $e->getMessage());
            exit;
        }
    }

    // Actualizar el estado de la cita
    //
    //
    public function updateEstado() {
        try {
            $query = "UPDATE citas SET estado = :estado WHERE id_cita = :id_cita";
            $stmt = $this->con->prepare($query);

            $stmt->execute([
                'id_cita' => $this->id_cita,
                'estado' => $this->estado          
            ]);

            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función updateEstado: " . $e->getMessage());
            exit;
        }
    }

    // Obtener citas de un consultorio en un día seleccionado, excluyendo las canceladas
    //
    //
    private function getCitasByDay($id_consultorio, $fecha) { 
        try { 
            $query = 'SELECT horario FROM citas WHERE id_consultorio = :id_consultorio AND fecha = :fecha AND estado != "cancelada"'; 
            $stmt = $this->con->prepare($query); 
            $stmt->execute([
                'id_consultorio' => $id_consultorio, 
                'fecha' => $fecha 
            ]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) { 
            http_response_code(500); 
            error_log("[MODEL:Cita] Error en la función getCitasByDay: " . $e->getMessage()); 
            exit; 
        } 
    }

    //Obtener horarios de apertura y cierre de un consultorio
    //
    //
    private function getHorarioConsultorio($id_consultorio) {
        try {
            $query = 'SELECT abre_h, cierra_h FROM consultorios WHERE id_consultorio = :id_consultorio';
            $stmt = $this->con->prepare($query);
            $stmt->execute(['id_consultorio' => $id_consultorio]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500); 
            error_log("[MODEL:Cita] Error en la función getHorarioConsultorio: " . $e->getMessage()); 
            exit;
        } 
    }

    // Obtener horarios disponibles
    public function getHorariosDisponibles($id_consultorio, $fecha) {
        try {
            // Obtener horario de apertura y cierre del consultorio
            $horariosConsultorio = $this->getHorarioConsultorio($id_consultorio);
            if ($horariosConsultorio === false) {
                error_log("No se pudo obtener el horario del consultorio.");
                return [];
            }
            // error_log("Horario del consultorio: " . json_encode($horariosConsultorio));

            $horaInicio = new DateTime("$fecha " . $horariosConsultorio['abre_h']);
            $horaFin = new DateTime("$fecha " . $horariosConsultorio['cierra_h']);
            // Hora actual para no agregar intervalos menores
            $horaActual = new DateTime();

            // error_log("Hora de inicio: " . $horaInicio->format('Y-m-d H:i:s'));
            // error_log("Hora de fin: " . $horaFin->format('Y-m-d H:i:s'));
            // error_log("Hora actual: " . $horaActual->format('Y-m-d H:i:s'));

            // Si la hora actual ya pasó la hora de cierre para el día específico, no generar intervalos
            if ($horaActual > $horaFin && $fecha === $horaActual->format('Y-m-d')) {
                error_log("La hora actual ya pasó la hora de cierre del consultorio.");
                return [];
            }

            // Generar intervalos de tiempo disponibles de 30 minutos cada uno
            $intervalos = [];
            $intervalo = new DateInterval('PT30M'); // Intervalo de 30 minutos

            while ($horaInicio < $horaFin) {
                if ($horaInicio < $horaActual && $fecha === $horaActual->format('Y-m-d')) {
                    $horaInicio->add($intervalo); // Omitir horarios pasados
                    continue;
                }
                $intervalos[] = $horaInicio->format('H:i'); // Formatear solo horas y minutos
                $horaInicio->add($intervalo);
            }

            // error_log("Intervalos generados: " . json_encode($intervalos));

            // Consultar citas existentes en esa fecha y consultorio
            $citasOcupadas = $this->getCitasByDay($id_consultorio, $fecha);
            // Convertir las horas de las citas ocupadas a formato 'H:i' para comparación
            $citasOcupadas = array_map(function($horario) {
                return (new DateTime($horario))->format('H:i');
            }, $citasOcupadas);

            // error_log("Citas ocupadas: " . json_encode($citasOcupadas));

            $horariosDisponibles = array_diff($intervalos, $citasOcupadas);

            // error_log("Horarios disponibles: " . json_encode($horariosDisponibles));

            return array_values($horariosDisponibles);

        } catch (PDOException $e) {
            http_response_code(500);
            error_log("[MODEL:Cita] Error en la función getHorariosDisponibles: " . $e->getMessage()); 
            die;
        }
    }





    // Setters y Getters para cada atributo
    public function setIdCita($id_cita) { $this->id_cita = $id_cita; }
    public function setIdPaciente($id_paciente) { $this->id_paciente = $id_paciente; }
    public function setIConsultorio($id_consultorio) { $this->id_consultorio = $id_consultorio; }
    public function setPermisoHistorial($permiso_historial) { $this->permiso_historial = $permiso_historial; }
    public function setHorario($horario) { $this->horario = $horario; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setDatosAdicionales($datos_adicionales) { $this->datos_adicionales = $datos_adicionales; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setTipo_cita($tipo_cita) { $this->tipo_cita = $tipo_cita; }

    public function getIdCita() { return $this->id_cita; }
    public function getIdPaciente() { return $this->id_paciente; }
    public function getIdConsultorio() { return $this->id_consultorio; }
    public function getPermisoHistorial() { return $this->permiso_historial; }
    public function getHorario() { return $this->horario; }
    public function getFecha() { return $this->fecha; }
    public function getDatosAdicionales() { return $this->datos_adicionales; }
    public function getEstado() { return $this->estado; }
    public function getTipo_cita() { return $this->tipo_cita; }
}

?>
