<?php
/**
 * MusicLibrary class
 * @author herdyderp
 * @version 0.2.0
 */

define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

class MusicLibrary
{
	protected $app;
	protected $db;

	function __construct()
	{
		define('_JEXEC', 1);
		require_once(JPATH_BASE . '/includes/defines.php');
		require_once(JPATH_BASE . '/includes/framework.php');

		if(Version::MAJOR_VERSION === 4)
		{
			// swap session.web.site for session.web.administrator for admin apps
			$container = Factory::getContainer();
			$container->alias('session.web', 'session.web.site')
				->alias('session', 'session.web.site')
				->alias('JSession', 'session.web.site')
				->alias(\Joomla\CMS\Session\Session::class, 'session.web.site')
				->alias(\Joomla\Session\Session::class, 'session.web.site')
				->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

			$this->app = $container->get(\Joomla\CMS\Application\SiteApplication::class);
			// $this->app = $container->get(\Joomla\CMS\Application\AdministratorApplication::class);
			$this->app->createExtensionNamespaceMap(); // https://joomla.stackexchange.com/a/32146/41
			$this->app->loadLanguage(); /* allows modules to render */

			// Set the application as global app
			Factory::$application = $this->app;
		}
		else
		{
			$this->app = Factory::getApplication('site');
			$this->app->initialise();
		}

		$config              = json_decode(file_get_contents(JPATH_BASE . '/../msc_config.json'));
		$options['driver']   = $this->app->get('dbtype', 'mysqli');
		$options['host']     = $this->app->get('host', 'localhost');
		$options['user']     = $this->app->get('user', '');
		$options['password'] = $this->app->get('password', '');
		$options['database'] = $config->db_name;
		$options['prefix']   = $config->table_prefix;
		$this->db            = JDatabaseDriver::getInstance($options);
		$this->query         = $this->db->getQuery(true);
	}

	function __destruct()
	{
		$this->app = null;
		$this->db  = null;
	}

	/**
	 * Function to escape a text string
	 * @param string $text
	 * @return string $escaped
	 */
	function escape(string $text, bool $html = false)
	{
		if($html === true)
		{
			$escaped = htmlentities($text, ENT_COMPAT|ENT_HTML5, 'utf-8');
		}
		else
		{
			$escaped = htmlspecialchars($text, ENT_COMPAT|ENT_HTML5, 'utf-8');
		}

		return $escaped;
	}

	/**
	 * Function to get a list of artists.
	 *
	 * @return object $artist_list
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
	 * Function to get a total of items, albums, etc.
	 *
	 * @param string $type
	 * @return int $result
	 */
	function getTotalItems(string $type = 'all')
	{
		$this->query->clear();

		$this->query
			->select('COUNT(*) as count')
			->from($this->db->qn('#__albums', 'alb'));

		switch($type)
		{
			case 'music' :
				$this->query->where('alb.is_video = 0');
				break;
			case 'video' :
				$this->query->where('alb.is_video = 1');
				break;
			case 'all' :
			default :
				break;
		}

		try
		{
			$this->db->setQuery($this->query);

			$result = $this->db->loadResult();
		}
		catch(Exception $e)
		{
			$result = 0;
		}

		return $result;
	}

	/**
	 * Function to get a list of albums. Optionally allows for limiting by artist, media type, and pressing.
	 *
	 * @param int $artist_id
	 * @param string $type
	 * @param bool $first_press
	 * @return object $album_list
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

			$album_list = $this->db->loadObjectList();
		}
		catch(Exception $e)
		{
			$album_list        = new stdClass();
			$album_list->error = $e->getCode() . ': ' . $e->getMessage();
		}

		return $album_list;
	}
}
