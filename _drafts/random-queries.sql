-- Prepare current artist data for insertion into new table
INSERT INTO `msc_artists` (`artist_name`)
SELECT `artist`
FROM `j_album`
GROUP BY `artist`;


-- Prepare current album data for insertion into new table
INSERT INTO `msc_albums` (`artist_id`,`title`,`isbn`,`release_date`,`is_video`,`is_limited`,`is_first_press`)
SELECT
  -- `a`.`aid` AS id,
	`b`.`id` AS artist_id,
  `a`.`title`,
  -- `a`.`artist` AS artist_name,
  `a`.`cat` AS isbn,
  -- `a`.`sdate`,
  CAST(`a`.`sdate` AS DATE) AS release_date,
  -- `a`.`mtype`,
  -- `a`.`press_id`,
  CASE WHEN `a`.`mtype` = 'v' THEN 1 ELSE 0 END AS is_video,
  0 AS is_limited,
  CASE WHEN `a`.`press_id` = 'y' THEN 1 ELSE 0 END AS is_first_press
FROM `j_album` AS a
INNER JOIN `msc_artists` AS b ON `b`.`artist_name` = `a`.`artist`
ORDER BY `artist_name` ASC, `release_date` ASC;


-- Album list as a view (…probably won't actually use this >.>)
CREATE OR REPLACE SQL SECURITY INVOKER VIEW `msc_vw_album_list` AS
SELECT
  `alb`.`title` AS AlbumName,
  `art`.`artist_name` AS ArtistName,
  `alb`.`isbn` AS ISBN,
  `alb`.`release_date` AS ReleaseDate,
  CASE WHEN `alb`.`is_video` = 1 THEN 'Yes' ELSE 'No' END AS IsVideo,
  CASE WHEN `alb`.`is_limited` = 1 THEN 'Yes' ELSE 'No' END AS LimitedEdition,
  CASE WHEN `alb`.`is_first_press` = 1 THEN 'Yes' ELSE 'No' END AS FirstPress,
  `alb`.`num_discs` AS NumberOfDiscs,
  `alb`.`description` AS Description,
  `alb`.`language` AS Language
FROM `msc_albums` AS alb
INNER JOIN `msc_artists` AS art ON `art`.`id` = `alb`.`artist_id`
ORDER BY `artist_name` ASC, `release_date` ASC;
