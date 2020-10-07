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
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans+Condensed:ital,wght@0,300;1,300&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,700&display=swap" rel="stylesheet">
		<link href="media/css/style.css" rel="stylesheet">
		<script src="https://kit.fontawesome.com/31a434906f.js" crossorigin="anonymous" async defer></script>
	</head>
	<body>
		<main class="wrapper">
			<h1>Music Library</h1>

			<?php if($list->error) : ?>
				<p><?php echo $list->error; ?></p>

				<?php return false; ?>
			<?php endif; ?>

			<blockquote>
				<p>
					<?php echo $music->getTotalItems(); ?> items found, <?php echo $music->getTotalItems('video'); ?> of which are videos.<br>
					<small>
						<i class="fa fa-star" title="First press"></i> = First press<br>
						<i class="fa fa-history" title="Limited edition/availability"></i> = Limited edition/availability<br>
						<i class="fa fa-video" title="Video"></i> = Video
					</small>
				</p>
			</blockquote>

			<table class="music-library">
				<thead>
					<tr><th>Artist</th><th>Title</th><th>Catalog #</th><th>Release</th></tr>
				</thead>
				<tbody>
					<?php foreach($list as $i => $item) : ?>
						<?php $albums = $music->getAlbumList($item->id); ?>

						<?php foreach($albums as $k => $album) : ?>
							<tr class="music-library__album">
								<?php if($k === 0) : ?>
									<td class="music-library__artist" rowspan="<?php echo count($albums); ?>"><?php echo $music->escape($item->artist_name); ?></td>
								<?php endif; ?>

								<td>
									<?php echo nl2br($music->escape($album->album_name, true)); ?><br>
									<?php echo $album->first_press == 'Yes' ? '<i class="fas fa-star" title="First press"></i>' : ''; ?>
									<?php echo $album->limited_edition == 'Yes' ? '<i class="fas fa-history" title="Limited edition/availability"></i>' : ''; ?>
									<?php echo $album->is_video == 'Yes' ? '<i class="fas fa-video" title="Video"></i>' : ''; ?>
								</td>
								<td><?php echo $music->escape($album->isbn); ?></td>
								<td><?php echo $music->escape($album->release_date); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</main>

		<footer></footer>

		<script src="media/js/app.js"></script>
	</body>
</html>
