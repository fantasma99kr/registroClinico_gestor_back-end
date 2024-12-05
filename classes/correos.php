<?php


    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    //Clase para el envio de los correos
    //
    //
    class Email{

        //constructor
        //
        //
        public function __construct() {}

        //Envia un correo al medico para indicarle que se registro una nueva cita
        //
        //
        public function newCita($email, $fecha, $horario){
            $mail = new PHPMailer(true);
            try{
                //Server settings
                $mail->SMTPDebug = 0;  //Enable verbose debug output
                $mail->isSMTP();  //Send using SMTP
                $mail->Host = HOST_SMTP; //Set the SMTP server to send through
                $mail->SMTPAuth = true; //Enable SMTP authentication
                $mail->Username = SMTP_USERNAME; //SMTP username
                $mail->Password = PASSWORD_USER_SMTP; //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
                $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                //Datos del remitente para el envió del correo.
                $mail->setFrom(EMAIL_FROM_SMTP, 'FindMedicITT');
                // Destinatario
                $mail->addAddress($email);
                //Contenido del correo
                $mail->isHTML(true);
                /* Caracter */
                $mail->CharSet = 'UTF-8';               
                // Título         
                $mail->Subject = 'notificacion';
                /* Imagenes utilizadas en el correo */
                $mail->AddEmbeddedImage('img/logoITT.png', 'logo_itt');
                //Contenido HTML
                $mail->Body = '<!DOCTYPE html>
                <html>
                <head>
                    <link href="https://fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800" rel="stylesheet"> 
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Notificación de cita</title>
                </head>
                <body style="background-color:#FFFFFF;">
                      <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width:calc(100%);max-width:calc(554px);margin: 0 auto;">
                        <tr>
                          <td width="100%" style="text-align: left;">
                
                            <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td style="background-color:gray;padding:30px;text-align:center;color:white; font-family: Montserrat,sans-serif; font-size: 24px;">
                                        
                                    </td>
                                    <td td style="background-color:gray;padding:30px;text-align:center;color:white; font-family: Montserrat,sans-serif; font-size: 25px;">
                                        <span>    FindMedicITT    </span>
                                    </td>
                                    <td td style="background-color:gray;padding:30px;text-align:center;color:white; font-family: Montserrat,sans-serif; font-size: 24px;">
                                        <img src="cid:logo_itt" style="width:57px;">
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td style="background-color:lightgray;padding:30px;text-align:center;color:black; font-family: Montserrat,sans-serif;">
                                        <p>Se ha generado una cita para el dia: <strong>'. $fecha .'</strong> a la hora: <strong>'. $horario.'</strong></p>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" style="min-width:100%;">
                                <tr>
                                    <td width="100%" style="min-width:100%;background-color:#58585A;padding:10px;">
                                    </td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>';
                //Contenido para clientes que no utilicen HTML
                $mail->AltBody = "Hola se ha generado una nueva cita el dia: $fecha a la hora: $horario";
                $mail->send();
                return true;
            }catch(Exception $e){
                error_log("Correo::recoverEmail->Mail error:{$mail->ErrorInfo}");
                return false;
            }
        }








    }




?>