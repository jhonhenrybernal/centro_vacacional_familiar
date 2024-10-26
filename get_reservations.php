<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "centro_vacacional";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $start = $data->start;
    $end = $data->end;

    $stmt = $conn->prepare("INSERT INTO reservations (start_date, end_date) VALUES (?, ?)");
    $stmt->bind_param("ss", $start, $end);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Reserva exitosa"]);
    } else {
        echo json_encode(["message" => "Error en la reserva"]);
    }

    $stmt->close();
}
$conn->close();
?>
