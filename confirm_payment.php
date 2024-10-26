<?php
require 'vendor/autoload.php'; // Cargar dotenv si es necesario

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$authToken = $_ENV['AUTH_TOKEN'];

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar el método de solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar el token de autenticación en el encabezado
    $headers = getallheaders();
    if (!isset($headers['Authorization']) || $headers['Authorization'] !== "Bearer $authToken") {
        http_response_code(401);
        echo json_encode(["message" => "Acceso no autorizado"]);
        exit;
    }

    // Leer datos de entrada
    $data = json_decode(file_get_contents("php://input"));
    $reservationId = $data->reservationId;

    $stmt = $conn->prepare("UPDATE reservas SET confirmPay = ? WHERE id = ?");
    $confirmPay = 1;
    $stmt->bind_param("ii", $confirmPay, $reservationId);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Pago confirmado exitosamente"]);
    } else {
        echo json_encode(["message" => "Error al confirmar el pago"]);
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(["message" => "Método no permitido"]);
}
$conn->close();
?>
