<?php

    //Constantes de la base de datos
    require_once __DIR__ . "/../classes/config.php";

    class DataBase
    {

        //Datos de la conexion
        private $host;
        private $db;
        private $user;
        private $password;
        private $charset;
        public $con; //Variable para la conexion a la base de datos

        //Constructor establece define las variables y hace la conexion a la base de datos
        public function __construct(){
            //Obtiene los valores de las contantes
            $this->host = constant('HOST');
            $this->db = constant('DB');
            $this->user = constant('USER');
            $this->password = constant('PASSWORD');
            $this->charset = constant('CHARSET');
            $this->con = $this->getConexion();
        }

        //Conexion a la base de datos
        public function getConexion(){

            try{
                
                //Conexion a la base de datos con PDO
                $connection = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Manda errores de PDOException
                    PDO::ATTR_EMULATE_PREPARES => false, // Evita inyección SQL
                ];

                $pdo = new PDO($connection, $this->user, $this->password, $options);

                //Verifica que la conexion sea correcta
                // if($pdo){
                //     error_log("Conexion correcta");
                // }
                
                //Regresa el objeto de la conexion a la base de datos
                return $pdo;

            }catch (PDOException $e){
                //Mensaje de error interno
                http_response_code(500);
                error_log("Error en la conexion a la base de datos:" . $e->getMessage());
            }
        }

    }

?>