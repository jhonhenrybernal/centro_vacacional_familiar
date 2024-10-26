CREATE DATABASE vacation_rentals;
USE vacation_rentals;

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);

CREATE TABLE centro_vacacional.reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL
);

ADD COLUMN whatsapp VARCHAR(20);
-- 2. Insertar una nueva reserva
INSERT INTO centro_vacacional.reservas (start_date, end_date, name, email, whatsapp)
VALUES ('2024-11-07', '2024-11-10', 'Juan Pérez', 'juan@example.com', '1234567890');

-- 3. Consultar reservas en un rango de fechas
SELECT * FROM centro_vacacional.reservas
WHERE (start_date <= '2024-11-30' AND end_date >= '2024-11-01');

-- 4. Verificar disponibilidad de fechas para evitar conflictos
SELECT * FROM centro_vacacional.reservas
WHERE (start_date <= '2024-11-10' AND end_date >= '2024-11-07');


ALTER TABLE centro_vacacional.reservas
ADD COLUMN confirmPay BOOLEAN DEFAULT FALSE;


ALTER TABLE centro_vacacional.reservas
ADD COLUMN dateCreate DATETIME DEFAULT CURRENT_TIMESTAMP;