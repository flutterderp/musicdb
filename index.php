<?php
require_once(__DIR__ . '/MusicLibrary.php');

$music = new MusicLibrary();
$list  = $music->getArtistList();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<title>Music Library</title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300i|Open+Sans:400,700&display=swap" rel="stylesheet">
		<link href="media/css/style.css" rel="stylesheet">
	</head>
	<body>
		<main class="wrapper">
			<table class="music-library">
				<thead>
					<tr>
						<th aria-label="Album Title">&nbsp;</th>
						<th aria-label="ISBN/Catalog #">ISBN/Catalog #</th>
						<th aria-label="Release Date">Release Date</th>
					</tr>
				</thead>
				<tbody>
					<?php if(!isset($list->error)) : ?>
						<?php foreach($list as $i => $item) : ?>
							<tr class="music-library__artist">
								<td colspan="3">
									<a href="#" data-toggle-id="<?php echo $item->id; ?>">
										<?php echo htmlspecialchars($item->artist_name, ENT_QUOTES, 'utf-8'); ?>
									</a>
								</td>
							</tr>
							<?php
							$albums = $music->getAlbumList($item->id);

							if(!isset($albums->error)) : ?>
								<?php foreach($albums as $k => $album) : ?>
									<tr class="music-library__album <?php echo $k%2 === 1 ? 'striped' : ''; ?>" data-toggle-content="<?php echo $item->id; ?>" hidden>
										<td><?php echo nl2br($music->escape($album->album_name, true)); ?></td>
										<td><?php echo $music->escape($album->isbn); ?></td>
										<td><?php echo $music->escape($album->release_date); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<tr><td><?php echo $list->error; ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</main>

		<footer>

		</footer>

		<script src="media/js/app.js"></script>
	</body>
</html>
