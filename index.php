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
			<div class="music-library">
				<?php if(!isset($list->error)) : ?>
					<?php foreach($list as $i => $item) : ?>
						<section class="music-library__artist">
							<a href="#" data-toggle-id="<?php echo $item->id; ?>">
								<?php echo htmlspecialchars($item->artist_name, ENT_QUOTES, 'utf-8'); ?>
							</a>

							<?php
								$albums = $music->getAlbumList($item->id);

								if(!isset($albums->error)) : ?>
									<div class="music-library__album row">
										<?php foreach($albums as $k => $album) : ?>
											<div class="column hide-display" data-toggle-content="<?php echo $item->id; ?>">
												<?php echo nl2br($music->escape($album->album_name, true)); ?><br>
												<?php echo $music->escape($album->isbn); ?><br>
												<?php echo $music->escape($album->release_date); ?>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
						</section>
					<?php endforeach; ?>
				<?php else : ?>
					<p><?php echo $list->error; ?></p>
				<?php endif; ?>
			</div>
		</main>

		<footer>

		</footer>

		<script src="media/js/app.js"></script>
	</body>
</html>
