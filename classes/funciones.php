<?php

    //Funciones de servicio

    class Funciones{
        
        public function __construct(){}

        //Verifica si los parametros POST enviados estan definidos
        function existPOST($params){
            foreach($params as $param){
                if(!isset($_POST[$param])){
                    //Si no existe regresa un false
                    error_log('existPost -> No existe el parametro: ' . $param);
                    return false;
                }
            }
            //Si existen regresa un true
            return true;
        }

        //Verifica si los parametos GET existen
        function existGET($params){
            foreach($params as $param){
                if(!isset($_GET[$param])){
                    error_log('existGET -> No existe el parametro' . $param);
                    return false;
                }
            }
            return true;
        }

        //obtiene el valor del parametro GET y quita los caracteres especiales para evitar inyección de código y quita espacios en blanco
        function getGet($param){
            return trim(htmlspecialchars($_GET[$param]));
        }

        //Obtiene el valor del parametro POST y quita los caracteres especiales para evitar inyección de código y quita los espacios en blanco
        function getPost($param){
            return trim(htmlspecialchars($_POST[$param]));
        }

    }

?>