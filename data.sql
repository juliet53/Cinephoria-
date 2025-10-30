

-- cinema
CREATE TABLE `cinema` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(255) NOT NULL,
  `ville` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- genre
CREATE TABLE `genre` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- film
CREATE TABLE `film` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` LONGTEXT,
  `crush` TINYINT(1) DEFAULT 0,
  `director` VARCHAR(255) NOT NULL,
  `image_name` VARCHAR(255) DEFAULT NULL,
  `image_size` INT(11) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- film_genre
CREATE TABLE `film_genre` (
  `film_id` INT(11) NOT NULL,
  `genre_id` INT(11) NOT NULL,
  PRIMARY KEY (`film_id`, `genre_id`),
  KEY `IDX_FILM_GENRE_FILM` (`film_id`),
  KEY `IDX_FILM_GENRE_GENRE` (`genre_id`),
  CONSTRAINT `FK_FILM_GENRE_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FILM_GENRE_GENRE` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE
);

-- user
CREATE TABLE `user` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(180) NOT NULL,
  `roles` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (JSON_VALID(`roles`)),
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_USER_EMAIL` (`email`)
);

-- salle
CREATE TABLE `salle` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` INT(11) NOT NULL,
  `capacite` INT(11) NOT NULL,
  `qualite` VARCHAR(255) DEFAULT NULL,
  `cinema_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_SALLE_CINEMA` (`cinema_id`),
  CONSTRAINT `FK_SALLE_CINEMA` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`id`) ON DELETE SET NULL
);

-- seance
CREATE TABLE `seance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `film_id` INT(11) DEFAULT NULL,
  `salle_id` INT(11) DEFAULT NULL,
  `date_heure_debut` DATETIME NOT NULL,
  `date_heure_fin` DATETIME NOT NULL,
  `qualite` VARCHAR(255) NOT NULL,
  `place_disponible` INT(11) NOT NULL,
  `prix` DOUBLE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_SEANCE_FILM` (`film_id`),
  KEY `IDX_SEANCE_SALLE` (`salle_id`),
  CONSTRAINT `FK_SEANCE_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_SEANCE_SALLE` FOREIGN KEY (`salle_id`) REFERENCES `salle` (`id`) ON DELETE SET NULL
);

-- reservation
CREATE TABLE `reservation` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `seance_id` INT(11) DEFAULT NULL,
  `place_reserve` INT(11) NOT NULL,
  `prix` DOUBLE NOT NULL,
  `seats` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (JSON_VALID(`seats`)),
  `qr_code_path` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_RESERVATION_USER` (`user_id`),
  KEY `IDX_RESERVATION_SEANCE` (`seance_id`),
  CONSTRAINT `FK_RESERVATION_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_RESERVATION_SEANCE` FOREIGN KEY (`seance_id`) REFERENCES `seance` (`id`) ON DELETE SET NULL
);

-- incident
CREATE TABLE `incident` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `salle_id` INT(11) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `statut` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_INCIDENT_SALLE` (`salle_id`),
  CONSTRAINT `FK_INCIDENT_SALLE` FOREIGN KEY (`salle_id`) REFERENCES `salle` (`id`) ON DELETE CASCADE
);

-- avis
CREATE TABLE `avis` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `film_id` INT(11) DEFAULT NULL,
  `note` DOUBLE DEFAULT NULL,
  `commentaire` LONGTEXT DEFAULT NULL,
  `valide` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `IDX_AVIS_USER` (`user_id`),
  KEY `IDX_AVIS_FILM` (`film_id`),
  CONSTRAINT `FK_AVIS_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_AVIS_FILM` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE SET NULL
);

