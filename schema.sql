-- Esquema de Base de Datos para VetMedik
-- Sistema de Gestión para Clínicas Veterinarias, Citas, Mascotas, Pagos y Reportes

CREATE DATABASE IF NOT EXISTS vetmedik;
USE vetmedik;
SET sql_mode = '';

-- Tabla de usuarios (Administradores y Recepcionistas)
CREATE TABLE IF NOT EXISTS user (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    name VARCHAR(50),
    lastname VARCHAR(50),
    email VARCHAR(255),
    password VARCHAR(60),
    is_active BOOLEAN NOT NULL DEFAULT 1,
    is_admin BOOLEAN NOT NULL DEFAULT 0,
    created_at DATETIME
);

-- Inserción de usuario administrador inicial (password: admin -> sha1(md5("admin")))
INSERT INTO user (username, name, lastname, email, password, is_admin, is_active, created_at)
VALUES ("admin", "Administrador", "Veterinaria", "admin@vetmedik.local", SHA1(MD5("admin")), 1, 1, NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Especialidades y Áreas Veterinarias
CREATE TABLE IF NOT EXISTS category (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL
);

INSERT INTO category (id, name) VALUES
(1, "Medicina General y Consulta"),
(2, "Cirugía y Esterilización"),
(3, "Vacunación y Desparasitación"),
(4, "Dermatología Veterinaria"),
(5, "Estética y Peluquería Canina/Felina"),
(6, "Odontología y Limpieza Dental")
ON DUPLICATE KEY UPDATE id=id;

-- Médicos Veterinarios
CREATE TABLE IF NOT EXISTS medic (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    no VARCHAR(50),
    name VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    gender VARCHAR(1) DEFAULT 'M',
    day_of_birth DATE,
    email VARCHAR(255),
    address VARCHAR(255),
    phone VARCHAR(255),
    image VARCHAR(255) DEFAULT '',
    is_active BOOLEAN NOT NULL DEFAULT 1,
    category_id INT,
    created_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE SET NULL
);

INSERT INTO medic (id, no, name, lastname, gender, email, phone, category_id, is_active, created_at) VALUES
(1, 'MVZ-1045', 'Alejandro', 'Torres Morales', 'M', 'alejandro.torres@vetmedik.local', '555-401-2030', 1, 1, NOW()),
(2, 'MVZ-2089', 'Sofía', 'Camacho Delgado', 'F', 'sofia.camacho@vetmedik.local', '555-502-3040', 2, 1, NOW()),
(3, 'MVZ-3112', 'Daniela', 'Ramos Castillo', 'F', 'daniela.ramos@vetmedik.local', '555-603-4050', 5, 1, NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Pacientes / Mascotas y Dueños
CREATE TABLE IF NOT EXISTS pacient (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    no VARCHAR(50),                         -- Expediente / Microchip
    name VARCHAR(100) NOT NULL,              -- Nombre de la mascota
    species VARCHAR(50) DEFAULT 'Canino',    -- Especie (Canino, Felino, Ave, etc.)
    breed VARCHAR(100) DEFAULT '',           -- Raza
    gender VARCHAR(10) DEFAULT 'Macho',      -- Macho / Hembra
    color VARCHAR(50) DEFAULT '',
    weight DOUBLE DEFAULT 0.0,               -- Peso en Kg
    day_of_birth DATE,                       -- Fecha nacimiento aproximada
    owner_name VARCHAR(100) NOT NULL,        -- Nombre del Dueño / Tutor
    owner_phone VARCHAR(50) DEFAULT '',
    owner_email VARCHAR(255) DEFAULT '',
    owner_address VARCHAR(255) DEFAULT '',
    image VARCHAR(255) DEFAULT '',
    sick VARCHAR(500) DEFAULT '',            -- Antecedentes / Padecimientos
    medicaments VARCHAR(500) DEFAULT '',     -- Medicación continua
    alergy VARCHAR(500) DEFAULT '',          -- Alergias conocidas
    is_favorite BOOLEAN NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME
);

INSERT INTO pacient (id, no, name, species, breed, gender, color, weight, day_of_birth, owner_name, owner_phone, owner_email, owner_address, created_at) VALUES
(1, 'VET-001', 'Max', 'Canino', 'Golden Retriever', 'Macho', 'Dorado', 28.5, '2021-04-12', 'Carlos Méndez', '555-111-2233', 'carlos.m@email.com', 'Av. Central #45', NOW()),
(2, 'VET-002', 'Luna', 'Felino', 'Siamés', 'Hembra', 'Blanco con café', 4.2, '2022-08-20', 'Beatriz Silva', '555-222-3344', 'beatriz.s@email.com', 'Calle Rosales #12', NOW()),
(3, 'VET-003', 'Rocky', 'Canino', 'Bulldog Francés', 'Macho', 'Atigrado', 12.0, '2023-01-15', 'Fernando Ruiz', '555-333-4455', 'fernando.r@email.com', 'Paseo del Río #88', NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Estados de la cita / consulta
CREATE TABLE IF NOT EXISTS status (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO status (id, name) VALUES
(1, "Pendiente"),
(2, "Atendida / Realizada"),
(3, "No asistió"),
(4, "Cancelada")
ON DUPLICATE KEY UPDATE id=id;

-- Estados de pago general
CREATE TABLE IF NOT EXISTS payment (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO payment (id, name) VALUES
(1, "Pendiente"),
(2, "Pagado"),
(3, "Anulado")
ON DUPLICATE KEY UPDATE id=id;

-- Citas y Consultas Médicas
CREATE TABLE IF NOT EXISTS reservation (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,             -- Motivo / Tipo de Servicio
    note TEXT,                               -- Observaciones médicas
    message TEXT,                            -- Indicaciones al propietario
    date_at VARCHAR(50) NOT NULL,            -- Fecha YYYY-MM-DD
    time_at VARCHAR(50) NOT NULL,            -- Hora HH:MM
    created_at DATETIME,
    pacient_id INT NOT NULL,                 -- Mascota
    medic_id INT NOT NULL,                   -- Veterinario
    user_id INT NOT NULL,                    -- Operador
    symtoms TEXT,                            -- Síntomas reportados
    sick TEXT,                               -- Diagnóstico clínico
    medicaments TEXT,                        -- Receta / Tratamiento
    price DOUBLE DEFAULT 0.0,                -- Costo del servicio
    is_web BOOLEAN NOT NULL DEFAULT 0,
    payment_id INT NOT NULL DEFAULT 1,       -- 1=Pendiente, 2=Pagado, 3=Anulado
    status_id INT NOT NULL DEFAULT 1,        -- 1=Pendiente, 2=Atendida, etc.
    FOREIGN KEY (payment_id) REFERENCES payment(id),
    FOREIGN KEY (status_id) REFERENCES status(id),
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (pacient_id) REFERENCES pacient(id) ON DELETE CASCADE,
    FOREIGN KEY (medic_id) REFERENCES medic(id) ON DELETE CASCADE
);

INSERT INTO reservation (id, title, note, date_at, time_at, created_at, pacient_id, medic_id, user_id, symtoms, sick, medicaments, price, payment_id, status_id) VALUES
(1, 'Chequeo General y Vacuna Séxtuple', 'Mascota en buen estado nutricional', CURDATE(), '10:00', NOW(), 1, 1, 1, 'Vacunación anual', 'Sano', 'Aplicación de vacuna séxtuple', 450.00, 2, 2),
(2, 'Revisión Dermatológica', 'Presenta comezón en orejas', CURDATE(), '11:30', NOW(), 2, 1, 1, 'Rascado frecuente', 'Otitis externa leve', 'Gotas óticas antibióticas cada 12h por 7 días', 350.00, 1, 1),
(3, 'Corte de Pelo y Baño Medicado', 'Estética completa y corte de uñas', CURDATE(), '15:00', NOW(), 3, 3, 1, 'Estética de rutina', 'Buen estado general', 'Champú dermatológico', 300.00, 1, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Tabla de Registro de Pagos por Servicios
CREATE TABLE IF NOT EXISTS payment_record (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,             -- Cita o consulta liquidada
    pacient_id INT NOT NULL,                 -- Mascota
    amount DOUBLE NOT NULL,                  -- Monto cobrado
    payment_method VARCHAR(50) DEFAULT 'Efectivo', -- Efectivo, Tarjeta, Transferencia
    notes VARCHAR(255) DEFAULT '',           -- Notas adicionales / Folio
    user_id INT NOT NULL,                    -- Operador que recibió el pago
    created_at DATETIME,
    FOREIGN KEY (reservation_id) REFERENCES reservation(id) ON DELETE CASCADE,
    FOREIGN KEY (pacient_id) REFERENCES pacient(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

INSERT INTO payment_record (id, reservation_id, pacient_id, amount, payment_method, notes, user_id, created_at) VALUES
(1, 1, 1, 450.00, 'Tarjeta de Débito', 'Cobro de consulta y vacuna séxtuple', 1, NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Parámetros del Sistema
CREATE TABLE IF NOT EXISTS setting (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    label VARCHAR(255) NOT NULL,
    kind INT DEFAULT 1,
    val TEXT,
    cfg_id INT DEFAULT 1
);

INSERT INTO setting (name, label, val) VALUES 
("title", "Título del Sistema", "VetMedik"),
("admin_email", "Correo Administrador", "admin@vetmedik.local")
ON DUPLICATE KEY UPDATE id=id;
