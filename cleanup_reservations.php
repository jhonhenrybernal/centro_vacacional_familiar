<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Seleccionar reservas que cumplen el criterio para informar en el correo
$sql_select = "SELECT id, name, email, whatsapp, start_date, end_date, dateCreate 
               FROM reservas 
               WHERE confirmPay = 0 AND dateCreate <= NOW() - INTERVAL 3 DAY";
$result = $conn->query($sql_select);

$deletedReservations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deletedReservations[] = $row;
    }
}

// Ejecutar la eliminación de reservas
$sql_delete = "DELETE FROM reservas WHERE confirmPay = 0 AND dateCreate <= NOW() - INTERVAL 3 DAY";
if ($conn->query($sql_delete) === TRUE) {
    echo "Reservas no confirmadas eliminadas exitosamente";
} else {
    echo "Error al eliminar reservas no confirmadas: " . $conn->error;
}

$conn->close();

// Si hay reservas eliminadas, enviar correo al administrador
if (!empty($deletedReservations)) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->CharSet = 'UTF-8';

        // Configuración del correo
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress('pruebascorreosbernal@gmail.com', 'Administrador');
        $mail->isHTML(true);
        $mail->Subject = 'Notificación de Reservas Eliminadas';

        // Generar el contenido del correo
        $bodyContent = "<h1>Reservas No Confirmadas Eliminadas</h1>";
        $bodyContent .= "<p>Las siguientes reservas no confirmadas han sido eliminadas:</p>";
        $bodyContent .= "<ul>";
        
        foreach ($deletedReservations as $reservation) {
            $bodyContent .= "<li><strong>ID de Reserva:</strong> {$reservation['id']}<br>";
            $bodyContent .= "<strong>Nombre del Cliente:</strong> {$reservation['name']}<br>";
            $bodyContent .= "<strong>Correo:</strong> {$reservation['email']}<br>";
            $bodyContent .= "<strong>WhatsApp:</strong> {$reservation['whatsapp']}<br>";
            $bodyContent .= "<strong>Fechas:</strong> del {$reservation['start_date']} al {$reservation['end_date']}<br>";
            $bodyContent .= "<strong>Fecha de Creación:</strong> {$reservation['dateCreate']}</li><br><br>";
        }

        $bodyContent .= "</ul>";
        $mail->Body = $bodyContent;

        $mail->send();
        echo "Correo de notificación enviado al administrador.";
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
} else {
    echo "No hay reservas eliminadas para informar.";
}
?>
