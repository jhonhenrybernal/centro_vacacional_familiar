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
    $name = $data->name;
    $email = $data->email;
    $whatsapp = $data->whatsapp;

    $stmt = $conn->prepare("INSERT INTO reservations (start_date, end_date, name, email, whatsapp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $start, $end, $name, $email, $whatsapp);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Reserva confirmada exitosamente"]);
    } else {
        echo json_encode(["message" => "Error en la reserva"]);
    }

    $stmt->close();
}
$conn->close();
?>
