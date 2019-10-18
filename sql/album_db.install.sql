-- MySQL Script generated by MySQL Workbench
-- Wed Dec 19 01:00:59 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

-- -----------------------------------------------------
-- Table `msc_artists`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `msc_artists`;

CREATE TABLE IF NOT EXISTS `msc_artists` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `artist_name` TEXT NOT NULL,
  `alt_name` VARCHAR(512) NOT NULL DEFAULT '',
  `description` TEXT,
  `language` VARCHAR(45) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `idx_name` (`artist_name`(512) ASC),
  INDEX `idx_alt_name` (`alt_name` ASC),
  INDEX `idx_language` (`language` ASC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- Trigger to update artist's created time
DELIMITER $$
CREATE TRIGGER `new_artist` BEFORE INSERT ON `msc_artists`
FOR EACH ROW
  IF(NEW.created IS NULL OR NEW.created = '0000-00-00 00:00:00') THEN
    SET time_zone = '+0:00', NEW.created = CURRENT_TIMESTAMP;
  END IF$$
DELIMITER ;

-- Trigger to update artist's modified time
CREATE TRIGGER `update_artist` BEFORE UPDATE ON `msc_artists`
FOR EACH ROW
  SET time_zone = '+0:00', NEW.modified = CURRENT_TIMESTAMP;


-- -----------------------------------------------------
-- Table `msc_albums`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `msc_albums`;

CREATE TABLE IF NOT EXISTS `msc_albums` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `artist_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `title` TEXT NOT NULL,
  `isbn` VARCHAR(45) NOT NULL,
  `release_date` DATE NOT NULL DEFAULT '1970-01-01',
  `is_video` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_limited` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_first_press` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  `num_discs` INT(5) UNSIGNED NOT NULL DEFAULT 1,
  `description` TEXT,
  `language` VARCHAR(45) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `idx_title` (`title`(512) ASC),
  INDEX `idx_isbn` (`isbn` ASC),
  INDEX `idx_release_date` (`release_date` ASC),
  INDEX `idx_is_video` (`is_video` ASC),
  INDEX `idx_is_limited` (`is_limited` ASC),
  INDEX `idx_is_first_press` (`is_first_press` ASC),
  INDEX `fk_album_artist_idx` (`artist_id` ASC),
  CONSTRAINT `fk_album_artist`
    FOREIGN KEY (`artist_id`)
    REFERENCES `msc_artists` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- Trigger to update artist's created time
DELIMITER $$
CREATE TRIGGER `new_album` BEFORE INSERT ON `msc_albums`
FOR EACH ROW
  IF(NEW.created IS NULL OR NEW.created = '0000-00-00 00:00:00') THEN
    SET time_zone = '+0:00', NEW.created = CURRENT_TIMESTAMP;
  END IF$$
DELIMITER ;

-- Trigger to update album's modified time
CREATE TRIGGER `update_album` BEFORE UPDATE ON `msc_albums`
FOR EACH ROW
  SET time_zone = '+0:00', NEW.modified = CURRENT_TIMESTAMP;


-- -----------------------------------------------------
-- Table `msc_album_tracks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `msc_album_tracks`;

CREATE TABLE IF NOT EXISTS `msc_album_tracks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '		',
  `artist_id` INT(11) UNSIGNED DEFAULT 0,
  `title` TEXT NOT NULL,
  `track_number` INT(5) UNSIGNED NOT NULL DEFAULT 0,
  `disc_number` INT(5) UNSIGNED NOT NULL DEFAULT 1,
  `description` TEXT,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `idx_title` (`title`(512) ASC),
  INDEX `fk_track_album_idx` (`album_id` ASC),
  INDEX `fk_track_artist_idx` (`artist_id` ASC),
  CONSTRAINT `fk_track_album`
    FOREIGN KEY (`album_id`)
    REFERENCES `msc_albums` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_track_artist`
    FOREIGN KEY (`artist_id`)
    REFERENCES `msc_artists` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- Trigger to update album track's created time
DELIMITER $$
CREATE TRIGGER `new_album_track` BEFORE INSERT ON `msc_album_tracks`
FOR EACH ROW
  IF(NEW.created IS NULL OR NEW.created = '0000-00-00 00:00:00') THEN
    SET time_zone = '+0:00', NEW.created = CURRENT_TIMESTAMP;
  END IF$$
DELIMITER ;

-- Trigger to update album track's modified time
CREATE TRIGGER `update_album_track` BEFORE UPDATE ON `msc_album_tracks`
FOR EACH ROW
  SET time_zone = '+0:00', NEW.modified = CURRENT_TIMESTAMP;

