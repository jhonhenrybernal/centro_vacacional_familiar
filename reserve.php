<?php
require 'vendor/autoload.php'; // Incluir PHPMailer y phpdotenv

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Conectar a la base de datos usando las variables de entorno
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

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
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';

            // Correo al cliente
            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
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

            // Correo al administrador
            $mail->clearAddresses();
            $mail->addAddress('pruebascorreosbernal@gmail.com', 'Administrador');
            $mail->Subject = 'Nueva Reserva Realizada';
            $mail->Body = "
                <h1>Notificación de Nueva Reserva</h1>
                <p>Se ha realizado una nueva reserva en la plataforma:</p>
                <p><strong>Nombre del cliente:</strong> $name</p>
                <p><strong>Correo:</strong> $email</p>
                <p><strong>Número de WhatsApp:</strong> $whatsapp</p>
                <p><strong>Fechas de la reserva:</strong> del $start al $end</p>
            ";
            $mail->send();

            echo json_encode(["message" => "Reserva exitosa y correos enviados"]);
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
