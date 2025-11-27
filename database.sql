-- 1. CREAR Y SELECCIONAR LA BASE DE DATOS

DROP DATABASE IF EXISTS `spotify deTemu`;

CREATE DATABASE `spotify deTemu` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `spotify deTemu`;


-- 2. CREACIÓN DE TABLAS

-- Tabla de Usuarios para el Login
CREATE TABLE Usuarios (
    usuario_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Artistas
CREATE TABLE Artistas (
    artista_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    genero_principal VARCHAR(50),
    biografia TEXT,
    imagen_url VARCHAR(255)
);

-- Tabla de Álbumes
CREATE TABLE Albumes (
    album_id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    artista_id INT NOT NULL,
    fecha_lanzamiento DATE,
    imagen_url VARCHAR(255),
    FOREIGN KEY (artista_id) REFERENCES Artistas(artista_id)
);

-- Tabla de Canciones
CREATE TABLE Canciones (
    cancion_id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    duracion_segundos INT,
    album_id INT,
    FOREIGN KEY (album_id) REFERENCES Albumes(album_id)
);

-- Tabla de relación N:M entre Canciones y Artistas (para colaboraciones)
CREATE TABLE Cancion_Artista (
    cancion_id INT,
    artista_id INT,
    PRIMARY KEY (cancion_id, artista_id),
    FOREIGN KEY (cancion_id) REFERENCES Canciones(cancion_id),
    FOREIGN KEY (artista_id) REFERENCES Artistas(artista_id)
);

-- Tabla para "Me Gusta" (Canciones favoritas)
CREATE TABLE Canciones_Favoritas (
    usuario_id INT,
    cancion_id INT,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, cancion_id),
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(usuario_id),
    FOREIGN KEY (cancion_id) REFERENCES Canciones(cancion_id)
);


-- 3. INSERCIÓN DE DATOS

-- Insertar Usuarios
INSERT INTO Usuarios (nombre_usuario, email, contrasena_hash) VALUES
('hugo_gabriel', 'hugo.gabriel.32m@gmail.com', '232006164h');


-- Insertar Artistas (todos los artistas originales con sus imágenes)
-- Orden de imágenes según especificación del usuario
INSERT INTO Artistas (nombre, genero_principal, imagen_url) VALUES
('DPR IAN', 'K-Pop', 'URL_IMAGEN_1_DPR_IAN'), -- ID 1 (Primera imagen - DPR IAN)
('Caifanes', 'Rock', 'URL_IMAGEN_2_CAIFANES'), -- ID 2 (Segunda imagen - Caifanes El Silencio)
('deftones', 'Alternative Metal', 'URL_IMAGEN_3_DEFTONES'), -- ID 3 (Tercera imagen - Deftones Diamond Eyes)
('Enjambre', 'Rock Alternativo', 'URL_IMAGEN_4_ENJAMBRE'), -- ID 4 (Cuarta imagen - Enjambre)
('Gorillaz', 'Alternative', 'URL_IMAGEN_5_GORILLAZ'), -- ID 5 (Quinta imagen - Gorillaz)
('Gustavo Cerati', 'Rock', 'URL_IMAGEN_6_GUSTAVO_CERATI'), -- ID 6 (Sexta imagen - Gustavo Cerati)
('Jose jose', 'Balada', 'URL_IMAGEN_7_JOSE_JOSE'), -- ID 7 (Séptima imagen - José José)
('Miranda!', 'Pop', 'URL_IMAGEN_MIRANDA'), -- ID 8 (Primera imagen - Miranda!)
('Soda Stereo', 'Rock', 'URL_IMAGEN_SODA_STEREO'), -- ID 9 (Segunda imagen - Soda Stereo)
('the beatles', 'Rock', 'URL_IMAGEN_THE_BEATLES'), -- ID 10 (Tercera imagen - The Beatles)
('the smiths', 'Indie Rock', 'URL_IMAGEN_THE_SMITHS'), -- ID 11 (Cuarta imagen - The Smiths)
('Zoé', 'Rock Alternativo', 'URL_IMAGEN_ZOE'); -- ID 12 (Quinta imagen - Zoé)


-- Insertar Álbumes
-- DPR IAN (ID 1)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Moodswings in This Order', 1, '2021-03-12', 'URL_IMAGEN_1_DPR_IAN'), -- ID 1
('Moodswings in to Order', 1, '2022-07-29', 'URL_IMAGEN_1_DPR_IAN'); -- ID 2

-- Caifanes (ID 2)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Caifanes', 2, '1988-01-01', 'URL_IMAGEN_2_CAIFANES'), -- ID 3
('El Diablito', 2, '1990-01-01', 'URL_IMAGEN_2_CAIFANES'), -- ID 4
('El Silencio', 2, '1992-01-01', 'URL_IMAGEN_2_CAIFANES'); -- ID 5

-- deftones (ID 3)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('White Pony', 3, '2000-06-20', 'URL_IMAGEN_3_DEFTONES'), -- ID 6
('Around the Fur', 3, '1997-10-28', 'URL_IMAGEN_3_DEFTONES'), -- ID 7
('Diamond Eyes', 3, '2010-05-04', 'URL_IMAGEN_3_DEFTONES'); -- ID 8

-- Enjambre (ID 4)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Enjambre', 4, '2020-01-01', 'URL_IMAGEN_4_ENJAMBRE'), -- ID 9
('Daltónico', 4, '2015-01-01', 'URL_IMAGEN_4_ENJAMBRE'); -- ID 10

-- Gorillaz (ID 5)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Demon Days', 5, '2005-05-11', 'URL_IMAGEN_5_GORILLAZ'), -- ID 11
('Plastic Beach', 5, '2010-03-03', 'URL_IMAGEN_5_GORILLAZ'); -- ID 12

-- Gustavo Cerati (ID 6)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Bocanada', 6, '1999-06-28', 'URL_IMAGEN_6_GUSTAVO_CERATI'), -- ID 13
('Siempre Es Hoy', 6, '2002-10-21', 'URL_IMAGEN_6_GUSTAVO_CERATI'); -- ID 14

-- Jose jose (ID 7)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('El Triste', 7, '1970-01-01', 'URL_IMAGEN_7_JOSE_JOSE'), -- ID 15
('Secretos', 7, '1983-01-01', 'URL_IMAGEN_7_JOSE_JOSE'); -- ID 16

-- Miranda! (ID 8)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Es Mentira', 8, '2004-01-01', 'URL_IMAGEN_MIRANDA'), -- ID 17
('Sin Restricciones', 8, '2007-01-01', 'URL_IMAGEN_MIRANDA'); -- ID 18

-- Soda Stereo (ID 9)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Canción Animal', 9, '1990-08-07', 'URL_IMAGEN_SODA_STEREO'), -- ID 19
('Dynamo', 9, '1992-08-10', 'URL_IMAGEN_SODA_STEREO'), -- ID 20
('Sueño Stereo', 9, '1995-06-13', 'URL_IMAGEN_SODA_STEREO'); -- ID 21

-- the beatles (ID 10)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Abbey Road', 10, '1969-09-26', 'URL_IMAGEN_THE_BEATLES'), -- ID 22
('Sgt. Pepper''s Lonely Hearts Club Band', 10, '1967-06-01', 'URL_IMAGEN_THE_BEATLES'), -- ID 23
('The Beatles (White Album)', 10, '1968-11-22', 'URL_IMAGEN_THE_BEATLES'), -- ID 24
('A Hard Day''s Night', 10, '1964-07-10', 'URL_IMAGEN_THE_BEATLES'); -- ID 25

-- the smiths (ID 11)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('The Queen Is Dead', 11, '1986-06-16', 'URL_IMAGEN_THE_SMITHS'), -- ID 26
('Meat Is Murder', 11, '1985-02-11', 'URL_IMAGEN_THE_SMITHS'), -- ID 27
('The Sound of The Smiths', 11, '2008-11-10', 'URL_IMAGEN_THE_SMITHS'); -- ID 28

-- Zoé (ID 12)
INSERT INTO Albumes (titulo, artista_id, fecha_lanzamiento, imagen_url) VALUES
('Rocanlover', 12, '2003-01-01', 'URL_IMAGEN_ZOE'), -- ID 29
('Memo Rex Commander y el Corazón Atómico de la Vía Láctea', 12, '2006-01-01', 'URL_IMAGEN_ZOE'), -- ID 30
('Zoé 2001-2010', 12, '2010-01-01', 'URL_IMAGEN_ZOE'); -- ID 31


-- Insertar Canciones
-- DPR IAN - Moodswings in This Order (Album ID 1)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('So Beautiful', 203, 1),
('Dope Lovers', 195, 1),
('No Blueberries', 198, 1);

-- DPR IAN - Moodswings in to Order (Album ID 2)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Calico', 185, 2),
('1 Shot', 192, 2),
('Winterfall', 201, 2);

-- Caifanes - Caifanes (Album ID 3)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('La Negra Tomasa', 245, 3),
('Viento', 240, 3),
('Mátenme Porque Me Muero', 235, 3),
('La Celula Que Explota', 250, 3);

-- Caifanes - El Diablito (Album ID 4)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Afuera', 245, 4),
('Nubes', 240, 4),
('Ayer Me Dijo un Ave', 235, 4);

-- Caifanes - El Silencio (Album ID 5)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Nubes', 240, 5),
('Viento', 245, 5),
('Perdí Mi Ojo de Venado', 250, 5);

-- deftones - White Pony (Album ID 6)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Change (In the House of Flies)', 299, 6),
('Digital Bath', 257, 6),
('Elite', 244, 6),
('Passenger', 373, 6);

-- deftones - Around the Fur (Album ID 7)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('My Own Summer (Shove It)', 199, 7),
('Be Quiet and Drive (Far Away)', 300, 7),
('Around the Fur', 237, 7);

-- deftones - Diamond Eyes (Album ID 8)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Diamond Eyes', 221, 8),
('Rocket Skates', 245, 8),
('Sextape', 238, 8);

-- Enjambre - Enjambre (Album ID 9)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Somos Ajenos', 245, 9),
('Tu Boca', 230, 9),
('Despertar', 220, 9);

-- Enjambre - Daltónico (Album ID 10)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Daltónico', 235, 10),
('Cicatriz', 240, 10),
('Eléctrico', 225, 10);

-- Gorillaz - Demon Days (Album ID 11)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Feel Good Inc.', 221, 11),
('DARE', 244, 11),
('Dirty Harry', 222, 11),
('Kids with Guns', 223, 11);

-- Gorillaz - Plastic Beach (Album ID 12)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Stylo', 268, 12),
('On Melancholy Hill', 230, 12),
('Rhinestone Eyes', 238, 12);

-- Gustavo Cerati - Bocanada (Album ID 13)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Puente', 240, 13),
('Tabú', 245, 13),
('Raíz', 250, 13),
('Bocanada', 235, 13);

-- Gustavo Cerati - Siempre Es Hoy (Album ID 14)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Cosas Imposibles', 245, 14),
('Karaoke', 240, 14),
('Artefacto', 235, 14);

-- Jose jose - El Triste (Album ID 15)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('El Triste', 203, 15),
('La Nave del Olvido', 195, 15),
('Almohada', 188, 15);

-- Jose jose - Secretos (Album ID 16)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('El Amor Acaba', 240, 16),
('Lo Que No Fue No Será', 225, 16),
('Secretos', 218, 16);

-- Miranda! - Es Mentira (Album ID 17)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Yo Te Diré', 200, 17),
('El Profe', 195, 17),
('Don', 190, 17);

-- Miranda! - Sin Restricciones (Album ID 18)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Perfecta', 205, 18),
('Enamorada', 200, 18),
('Tu Juego', 195, 18);

-- Soda Stereo - Canción Animal (Album ID 19)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('De Música Ligera', 214, 19),
('Un Millón de Años Luz', 280, 19),
('Canción Animal', 245, 19),
('Té para Tres', 195, 19);

-- Soda Stereo - Dynamo (Album ID 20)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Primavera 0', 240, 20),
('Fue', 245, 20),
('Luna Roja', 250, 20);

-- Soda Stereo - Sueño Stereo (Album ID 21)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Ella Usó Mi Cabeza Como un Revólver', 280, 21),
('Disco Eterno', 245, 21),
('Zoom', 240, 21);

-- the beatles - Abbey Road (Album ID 22)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Come Together', 259, 22),
('Something', 182, 22),
('Here Comes the Sun', 185, 22),
('The End', 141, 22);

-- the beatles - Sgt. Pepper''s (Album ID 23)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Sgt. Pepper''s Lonely Hearts Club Band', 122, 23),
('With a Little Help from My Friends', 164, 23),
('Lucy in the Sky with Diamonds', 208, 23),
('A Day in the Life', 335, 23);

-- the beatles - White Album (Album ID 24)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Back in the U.S.S.R.', 163, 24),
('While My Guitar Gently Weeps', 285, 24),
('Blackbird', 138, 24),
('Helter Skelter', 270, 24);

-- the beatles - A Hard Day's Night (Album ID 25)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('A Hard Day''s Night', 152, 25),
('I Should Have Known Better', 163, 25),
('If I Fell', 139, 25),
('And I Love Her', 161, 25);

-- the smiths - The Queen Is Dead (Album ID 26)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('The Queen Is Dead', 377, 26),
('I Know It''s Over', 345, 26),
('There Is a Light That Never Goes Out', 285, 26),
('Bigmouth Strikes Again', 208, 26);

-- the smiths - Meat Is Murder (Album ID 27)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('The Headmaster Ritual', 248, 27),
('Rusholme Ruffians', 252, 27),
('I Want the One I Can''t Have', 198, 27);

-- the smiths - The Sound of The Smiths (Album ID 28)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('How Soon Is Now?', 406, 28),
('This Charming Man', 164, 28),
('Panic', 137, 28);

-- Zoé - Rocanlover (Album ID 29)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Love', 245, 29),
('Asteroide', 240, 29),
('Vía Láctea', 250, 29);

-- Zoé - Memo Rex Commander (Album ID 30)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Poli', 245, 30),
('No Me Destruyas', 240, 30),
('Reptilectric', 250, 30);

-- Zoé - Zoé 2001-2010 (Album ID 31)
INSERT INTO Canciones (titulo, duracion_segundos, album_id) VALUES
('Soñé', 245, 31),
('Nada', 240, 31),
('Labios Rotos', 250, 31);

INSERT INTO Canciones_Favoritas (usuario_id, cancion_id)
SELECT 1, 17 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 60 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 75 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 25 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 7 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 41 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 50 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 56 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 48 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1)
UNION ALL SELECT 1, 88 WHERE EXISTS (SELECT 1 FROM Usuarios WHERE usuario_id = 1);
