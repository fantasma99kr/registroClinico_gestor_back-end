<?php

    class ControllerHisotorial{

        //Variables de instancia
        private $fun;
        private $historial;
        //Ruta de la carpeta de los archivos pdf
        private $rutaPDF = "pdf/";

        //Constructor
        //
        //
        public function __construct(){
            //Funciones generales
            $this->fun = new Funciones();
            //Instancia del modelo historial
            $this->historial = new HistorialClinico();
        }

        //Obtiene todos los datos
        //
        //
        public function get(){
            error_log($this->fun->getGet("id_paciente"));
            $data = $this->historial->getAll();
            http_response_code(201);
            echo json_encode($data);
            exit;
        }

        //Obtiene todos los datos de un usuario
        //
        //
        public function getById(){
            //Valida que se manden los parametros
            if($this->fun->existGET(['id_paciente'])){
                $this->historial->setIdPaciente($this->fun->getGet('id_paciente'));
                $data = $this->historial->getAllPaciente();
                http_response_code(201);
                echo json_encode($data);
                exit;
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        //Funcion utilizada para guardar el historial clinico
        //
        //
        public function save(){
            $maxFileSize = 5 * 1024 * 1024;//Tamaño maximo del archivo 5MB
            
            //Verifica que se manden los parametros
            if($this->fun->existPOST(['id_paciente','titulo','descripcion'])){

                //Id del paciente para crear el nuevo nombre del archivo
                $this->historial->setIdPaciente($this->fun->getPost('id_paciente'));
                
                // Verificar y crear la ruta de destino si no existe
                if (!is_dir($this->rutaPDF)) {
                    mkdir($this->rutaPDF, 0755, true); // Crea el directorio con los permisos
                }

                //Valida el tamaño del archivo sea menor a 5MB
                if(isset($_FILES['pdf'])){ //Verifica si el archivo no esta vacio
                    
                    if($_FILES['pdf']['size'] <=  $maxFileSize){//Verifica que el archivo no pase de 5MB
                        $pdfData = $_FILES['pdf']['tmp_name']; //Obtiene la ruta temporal del archivo
                        $fechaHora = date('_d_m_Y_H_i_s_');//Fecha y hora para el nombre del documento y evitar que se repita el nombre
                        $nameTem =  trim($_FILES['pdf']['name']);//Nombre original del documento
                        //Nuevo nombre del documento
                        $pdfname =  $this->historial->getIdPaciente() . $fechaHora . $nameTem;
                        
                        //Se guarda el archivo
                        if(!move_uploaded_file($pdfData, $this->rutaPDF . $pdfname)){
                            http_response_code(500);
                            echo json_encode(["message" => "Hubo un error al mover el archivo PDF."]); 
                            exit;
                        }
                    } else {
                        http_response_code(413);
                        echo json_encode(["message" => "El archivo es demasiado grande. Máximo permitido es 5 MB."]);
                        exit;
                    }
                } else {
                    //Si no hay ningun pdf se deja en blanco
                    $pdfname = "";
                }

                
                //Verifica que que se pasara una imagen
                if(isset($_FILES['image'])){ //Verifica si se subio el pdf
                    
                    //Valida que la imagen no pase de 5MB
                    if($_FILES['image']['size'] <=  $maxFileSize){
                        //Pasa la imagen a base 64
                        $imageData = $_FILES['image']['tmp_name'];
                        $image = imagecreatefromstring(file_get_contents($imageData)); 

                        if ($image !== false) {
                            //Redimensionar la imagen
                            $newWidth = 800;
                            $newHeight = 800;
                            list($width, $height) = getimagesize($imageData);
                            $compressedImage = imagecreatetruecolor($newWidth, $newHeight);
                            imagecopyresampled($compressedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                            //Guardar la imagen comprimida en un buffer
                            ob_start();
                            imagejpeg($compressedImage, null, 85);
                            $compressedImageData = ob_get_contents();
                            ob_end_clean();

                            // Convertir la imagen comprimida a base64
                            $imagen = base64_encode($compressedImageData);

                            // Liberar memoria
                            imagedestroy($image);
                            imagedestroy($compressedImage);
                        } else {
                            http_response_code(500);
                            echo json_encode(["message" => "Error al procesar la imagen."]);
                            exit;
                        }

                    } else {
                        http_response_code(413);
                        echo json_encode(["message" => "El archivo es demasiado grande. Máximo permitido es 5 MB."]);
                        exit;
                    }
                } else {
                    //Si no se sube una imagen se deja en blanco
                    $imagen = "";
                }
                
                //Se definen los valores del registro
                $this->historial->setTitulo($this->fun->getPost('titulo'));
                $this->historial->setDocumento($pdfname);
                $this->historial->setImagen($imagen);
                $this->historial->setDescripcion($this->fun->getPost('descripcion'));
                
                //Se registra la informacion
                $this->historial->save();
                http_response_code(201);
                echo json_encode(["message" => "Registro realizado correctamente"]);
                exit;
            }else{
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        //Descargar documento
        //
        //
        public function documento(){
            //Verifica que se mande el nombre del documento para descargar
            if($this->fun->existGET(['documento'])){
                //Nombre del documento
                $nomDocumento = $this->fun->getGet('documento');
                $dirarchivo = $this->rutaPDF . $nomDocumento;
                //Verifica que el documento este guardado
                if(file_exists($dirarchivo)){
                    //Definicion los encabezados para un archivo PDF 
                    header('Content-Type: application/pdf'); //Tipo de archivo pdf
                    //Nombre con el que se descargara
                    header('Content-Disposition: inline; filename="' . basename($dirarchivo) . '"');//Basename quita  la ruta y solo se queda con el nombre del archivo
                    header('Content-Length: ' . filesize($dirarchivo));//Namaño del archivo
                    
                    //Manda el archivo
                    readfile($dirarchivo);
                    http_response_code(200);
                    exit;
                } else {
                    http_response_code(404); 
                    echo json_encode(["message" => "Documento no encontrado."]); 
                    exit; 
                }
            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit; 
            }
        }

        //Funcion utilizada para actualizar un registro
        //
        //
        public function update(){
            $maxFileSize = 5 * 1024 * 1024;//Tamaño maximo del archivo 5MB
            //Veerifica que se manden todos los parametros
            if($this->fun->existPOST(['id_historial','titulo','descripcion'])){
                //Se asigna el valor del id del historial en el objeto
                $this->historial->setIdHistorial($this->fun->getPost('id_historial'));
                //Id del paciente para crear el nuevo nombre del archivo
                $this->historial->getById();

                //Valida el tamaño del archivo sea menor a 5MB
                if(isset($_FILES['pdf'])){ //Verifica si el archivo no esta vacio

                    $pdf = $this->historial->getDocumento();
                    
                    //Buscar si existe un archivo pdf que borrar deacuerdo al nombre del archivo que esta en la base de datos
                    if(file_exists($this->rutaPDF . $pdf) && $pdf !== ''){
                        //Borra el archivo y despues elimina el registro
                        if(!unlink($this->rutaPDF . $pdf)){
                            http_response_code(500);
                            echo json_encode(["message" => "Error al sustituir el documento."]);
                            exit;
                        }
                    }

                    if($_FILES['pdf']['size'] <=  $maxFileSize){//Verifica que el archivo no pase de 5MB
                        $pdfData = $_FILES['pdf']['tmp_name']; //Obtiene la ruta temporal del archivo
                        $fechaHora = date('_d_m_Y_H_i_s_');//Fecha y hora para el nombre del documento y evitar que se repita el nombre
                        $nameTem =  trim($_FILES['pdf']['name']);//Nombre original del documento
                        //Nuevo nombre del documento
                        $pdfname =  $this->historial->getIdPaciente() . $fechaHora . $nameTem;
                        
                        $this->historial->setDocumento($pdfname);
                        //Se guarda el archivo
                        if(!move_uploaded_file($pdfData, $this->rutaPDF . $pdfname)){
                            http_response_code(500);
                            echo json_encode(["message" => "Hubo un error al mover el archivo PDF."]); 
                            exit;
                        }
                    } else {
                        http_response_code(413);
                        echo json_encode(["message" => "El archivo es demasiado grande. Máximo permitido es 5 MB."]);
                        exit;
                    }
                }

                
                //Verifica que que se pasara una imagen
                if(isset($_FILES['image'])){ //Verifica si se subio el pdf
                    
                    //Valida que la imagen no pase de 5MB
                    if($_FILES['image']['size'] <=  $maxFileSize){
                        //Pasa la imagen a base 64
                        $imageData = $_FILES['image']['tmp_name'];
                        $image = imagecreatefromstring(file_get_contents($imageData)); 

                        if ($image !== false) {
                            //Redimensionar la imagen
                            $newWidth = 800;
                            $newHeight = 800;
                            list($width, $height) = getimagesize($imageData);
                            $compressedImage = imagecreatetruecolor($newWidth, $newHeight);
                            imagecopyresampled($compressedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                            //Guardar la imagen comprimida en un buffer
                            ob_start();
                            imagejpeg($compressedImage, null, 85);
                            $compressedImageData = ob_get_contents();
                            ob_end_clean();

                            // Convertir la imagen comprimida a base64
                            $this->historial->setImagen(base64_encode($compressedImageData));

                            // Liberar memoria
                            imagedestroy($image);
                            imagedestroy($compressedImage);
                        } else {
                            http_response_code(500);
                            echo json_encode(["message" => "Error al procesar la imagen."]);
                            exit;
                        }

                    } else {
                        http_response_code(413);
                        echo json_encode(["message" => "El archivo es demasiado grande. Máximo permitido es 5 MB."]);
                        exit;
                    }
                }
                
                //Se definen los valores del registro
                $this->historial->setTitulo($this->fun->getPost('titulo'));
                $this->historial->setDocumento($this->historial->getDocumento());
                $this->historial->setImagen($this->historial->getImagen());
                $this->historial->setDescripcion($this->fun->getPost('descripcion'));
                
                //Se registra la informacion
                $this->historial->update();
                http_response_code(201);
                echo json_encode(["message" => "Registro actualizado correctamente."]);
                exit;  
                

            } else {
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

        //Elimina un registro
        //
        //
        public function delete(){
            //Verifica que se manden todos los parametros 
            if($this->fun->existPOST(['id_historial'])){
                //Se asigna el valor del id del historial en el objeto 
                $this->historial->setIdHistorial($this->fun->getPost('id_historial'));
                
                //Verifica que el registro del historial exista y obtiene el valor de cada uno de sus atributos para poder
                //borrar el archivo pdf
                if($this->historial->getById()){
                    //Obtiene el nombre del documento
                    $pdf = $this->historial->getDocumento();
                    
                    //Buscar si existe un archivo pdf que borrar deacuerdo al nombre del archivo que esta en la base de datos
                    if(file_exists($this->rutaPDF . $pdf) && $pdf !== ''){
                       
                        //Borra el archivo y despues elimina el registro
                        if(unlink($this->rutaPDF . $pdf)){
                            $this->historial->delete();
                            http_response_code(200);
                            echo json_encode(["message" => "Registro eliminado correctamente"]);
                            exit;
                        } else{
                            http_response_code(500);
                            echo json_encode(["message" => "Error al borrar el archivo"]);
                            exit;
                        }
                    } else {//Si no existe se elimina directamente el registro
                        $this->historial->delete();
                        http_response_code(200);
                        echo json_encode(["message" => "Registro eliminado correctamente"]);
                        exit;
                    }
                } else {
                    http_response_code(404); 
                    echo json_encode(["message" => "El registro del historial no existe"]); 
                    exit; 
                }

            }else{
                http_response_code(400); 
                echo json_encode(["message" => "Faltan parametros"]); 
                exit;
            }
        }

    }


?>