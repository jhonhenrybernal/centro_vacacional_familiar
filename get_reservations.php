<?php
require 'vendor/autoload.php'; // Incluir dotenv si lo necesitas

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

// Consultar las reservas en la base de datos
$sql = "SELECT start_date, end_date FROM reservas";
$result = $conn->query($sql);

$reservations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'title' => 'Reservado',
            'start' => $row['start_date'],
            'end' => date('Y-m-d', strtotime($row['end_date'] . ' +1 day')), // Asegura que la fecha final sea inclusiva
            'color' => 'green'
        ];
    }
}

$conn->close();

// Devolver las reservas en formato JSON
header('Content-Type: application/json');
echo json_encode($reservations);
?>
