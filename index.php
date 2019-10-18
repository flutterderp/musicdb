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
	</head>
	<body>
		<main role="main">
			<table class="music-library">
				<thead>
					<tr>
						<th>Album</th>
						<th>ISBN/Catalog #</th>
						<th>Release Date</th>
					</tr>
				</thead>
				<tbody>
					<?php if(!isset($list->error)) : ?>
						<?php foreach($list as $i => $item) : ?>
							<tr class="music-library__artist">
								<td colspan="3"><a href="#"><?php echo htmlspecialchars($item->artist_name, ENT_QUOTES, 'utf-8'); ?></a></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr><td><?php echo $list->error; ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</main>
	</body>
</html>
