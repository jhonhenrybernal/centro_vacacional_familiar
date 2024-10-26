<?php
require 'vendor/autoload.php'; // Incluir PHPMailer si usas Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "centro_vacacional";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $start = $data->start;
    $end = $data->end;
    $name = $data->name;
    $email = $data->email;
    $whatsapp = $data->whatsapp;

    $stmt = $conn->prepare("INSERT INTO reservas (start_date, end_date, name, email, whatsapp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $start, $end, $name, $email, $whatsapp);
    if ($stmt->execute()) {
        try {
            $mail = new PHPMailer(true);
            // Configuración del servidor SMTP de Hostinger
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tecnologia@devquick.co'; // Cambia por tu correo de Hostinger
            $mail->Password = 'TecJulio2023#'; // Cambia por tu contraseña de Hostinger
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Cambiar a TLS para el puerto 587
            $mail->Port = 465;

            // Configuración del correo
            $mail->setFrom('tecnologia@devquick.co', 'Centro Vacacional');
            $mail->addAddress($email, $name); // Destinatario

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirmación de Reserva';
            $mail->Body = "
                <h1>Reserva Confirmada</h1>
                <p>Estimado/a $name,</p>
                <p>Gracias por reservar con nosotros. Tu reserva ha sido confirmada.</p>
                <p><strong>Fechas:</strong> del $start al $end</p>
                <p>Nos pondremos en contacto a través de WhatsApp al número $whatsapp.</p>
                <p>¡Esperamos verte pronto!</p>
            ";

            $mail->send();


              // Preparar y enviar correo al administrador
              $mail->clearAddresses(); // Limpiar direcciones previas
              $mail->addAddress('pruebascorreosbernal@gmail.com', 'Administrador');
              $mail->Subject = 'Nueva Reserva';
              $mail->Body = "
                  <h1>Notificación de Nueva Reserva</h1>
                  <p>Se ha realizado una nueva reserva en la plataforma:</p>
                  <p><strong>Nombre del cliente:</strong> $name</p>
                  <p><strong>Correo:</strong> $email</p>
                  <p><strong>Número de WhatsApp:</strong> $whatsapp</p>
                  <p><strong>Fechas de la reserva:</strong> del $start al $end</p>
              ";
              $mail->send(); // Enviar el correo al administrador
            echo json_encode(["message" => "Reserva exitosa y correo enviado"]);
        } catch (Exception $e) {
            echo json_encode(["message" => "Reserva exitosa, pero no se pudo enviar el correo. Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["message" => "Error en la reserva"]);
    }

    $stmt->close();
}
$conn->close();
?>
