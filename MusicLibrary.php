<?php
/**
 * MusicLibrary class
 * @author herdyderp
 * @version 0.1.0
 */

define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);

class MusicLibrary
{
	protected $app;
	protected $db;

	function __construct()
	{
		define('_JEXEC', 1);
		require_once(JPATH_BASE . '/includes/defines.php');
		require_once(JPATH_BASE . '/includes/framework.php');

		$config              = json_decode(file_get_contents(JPATH_BASE . '/../msc_config.json'));
		$this->app           = JFactory::getApplication('site');
		$this->app->initialise();
		$options['driver']   = $this->app->get('dbtype', 'mysqli');
		$options['host']     = $this->app->get('host', 'localhost');
		$options['user']     = $this->app->get('user', '');
		$options['password'] = $this->app->get('password', '');
		$options['database'] = $config->db_name;
		$options['prefix']   = $config->table_prefix;
		$this->db            = JDatabaseDriver::getInstance($options);;
		$this->query         = $this->db->getQuery(true);
	}

	function __destruct()
	{
		$this->app = null;
		$this->db  = null;
	}

	/**
	 * Function to get a list of artists.
	 *
	 * @return artist_list object
	 */
	function getArtistList()
	{
		$this->query->clear();
		$this->query->select('*')->from($this->db->qn('#__artists'))->order('artist_name ASC');

		try
		{
			$this->db->setQuery($this->query);
			$artist_list = $this->db->loadObjectList();
		}
		catch(Exception $e)
		{
			$artist_list        = new stdClass();
			$artist_list->error = $e->getCode() . ': ' . $e->getMessage();
		}

		return $artist_list;
	}

	/**
	 * Function to get a list of albums. Optionally allows for limiting by artist, media type, and pressing.
	 *
	 * @param artist_id int
	 * @param type string
	 * @param first_press bool
	 * @return album_list object
	 */
	function getAlbumList(int $artist_id = 0, string $type = '', bool $first_press = false)
	{
		$this->query->clear();

		$this->query
			->select('alb.id AS album_id, alb.artist_id AS artist_id')
			->select('alb.title AS album_name, art.artist_name, alb.isbn, alb.release_date')
			->select('CASE WHEN alb.is_video = 1 THEN ' . $this->db->q('Yes') . ' ELSE ' . $this->db->q('No') . ' END AS is_video')
			->select('CASE WHEN alb.is_limited = 1 THEN ' . $this->db->q('Yes') . ' ELSE ' . $this->db->q('No') . ' END AS limited_edition')
			->select('CASE WHEN alb.is_first_press = 1 THEN ' . $this->db->q('Yes') . ' ELSE ' . $this->db->q('No') . ' END AS first_press')
			->select('alb.num_discs, alb.description, alb.language')
			->from($this->db->qn('#__albums', 'alb'))
			->join('inner', $this->db->qn('#__artists', 'art') . ' ON art.id = alb.artist_id');

		//  Get albums from one artist only
		if(is_int($artist_id) && $artist_id > 0)
		{
			$this->query->where('alb.artist_id = ' . (int) $artist_id);
		}

		// Get either videos or music albums only
		if(is_string($type) && strtolower($type) === 'video')
		{
			$this->query->where('alb.is_video = 1');
		}
		elseif(is_string($type) && strtolower($type) === 'music')
		{
			$this->query->where('alb.is_video = 0');
		}

		// Get only first press albums
		if(is_bool($first_press) && $first_press === true)
		{
			$this->query->where('is_first_press = 1');
		}

		$this->query->order('artist_name ASC, release_date ASC');

		try
		{
			$this->db->setQuery($this->query);
			$album_list = $this->db->getObjectList();
		}
		catch(Exception $e)
		{
			$album_list        = new stdClass();
			$album_list->error = $e->getCode() . ': ' . $e->getMessage();
		}

		return $album_list;
	}
}
