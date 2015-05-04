<?php
/**
 * Plugin Name: NavColorizer
 * Plugin URI: http://github.com/pushrbx/navcolorizer-wp-avada
 * Description: Colorize you main menu items.
 * Version: 1.1
 * Author: pushrbx
 * Author URI: http://pushrbx.net/
 * License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

define('SNC_PATH', WP_PLUGIN_DIR  .'/navcolorizer');
define('SNC_URL', WP_PLUGIN_URL .'/navcolorizer');

class NavColorizer
{
	private static $instance = null;
	private static $activated = false;
	private $_options = array();
	private $countMenu = true;
	private $menuCount = 0;
	private $option_name = 'snc_options';

	public static function install()
	{
		self::$activated = true;

		$obj = self::getInstance();
		$obj->initOptions();
		//$obj->init();
	}

	public static function uninstall()
	{
		//unset(self::$instance);
		self::$instance->dispose(true);
		self::$instance = null;
	}

	public static function getInstance($options = array())
	{
		if(!isset(self::$instance))
			self::$instance = new NavColorizer($options);

		return self::$instance;
	}

	private function __construct($options)
	{
		if(!isset($options))
			throw new Exception("Argument was null: $options");

		$this->_options = $options;

		add_action('admin_init', array(&$this, 'admin_init'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('init', array(&$this, 'init'));

		$bn = plugin_basename(__FILE__);

		add_filter('plugin_action_links_'.$bn, array(&$this, 'getSettingsLink'));
	}

	function __destruct()
	{
		$this->dispose();
	}

	public function dispose($deactivate = false)
	{
		if($deactivate) delete_option('snc_options');
		// remove actions, hooks, filters
		remove_action('admin_init', array(&$this, 'admin_init'));
		remove_action('admin_menu', array(&$this, 'remove_menu'));
		remove_action('wp_enqueue_scripts', array(&$this, 'addMenuColors'));
		remove_action('wp_enqueue_scripts', array(&$this, 'addAdminScript'));
		remove_action('init', array(&$this, 'init'));
		remove_filter('plugin_action_links_$bn', array(&$this, 'getSettingsLink'));
		remove_filter( 'wp_nav_menu_objects', array(&$this, 'addMenuClass' ));
	}

	public function initOptions()
	{
		$options = get_option($this->option_name);
		if($options === false)
		{
			$options = array(
				//"colorcount" => 5,
				"colors" => array('#000', '#fcff00', '#00ff06', '#ff0000', '#0000ff')
			);
		}

		if(isset($_POST[$this->option_name]))
		{
			$options = sanitize_text_field($_POST[$this->option_name]);
		}

		update_option($this->option_name, $options);

		$this->_options = $options;
	}

	public function init()
	{
		$this->initOptions();

		add_action('wp_enqueue_scripts', array(&$this, 'addMenuColors'));
		add_filter( 'wp_nav_menu_objects', array(&$this, 'addMenuClass' ));
	}

	public function getMenuCount()
	{
		$menulocs = get_nav_menu_locations();
		$locid = "";

		foreach($menulocs as $loc => $id)
		{
			if(strpos($loc, "main") !== FALSE)
			{
				$locid = $id;
				break;
			}
		}
		$menu = get_term($locid, 'nav_menu');
		if($menu) return $menu->count;

		return 0;
	}

	public function addMenuClass($items)
	{
		if(!is_admin())
		{
			$colorIndex = 0;
			foreach ( $items as &$item )
			{
				if ($item->menu_item_parent != 0 ) { continue; }
				$cssClass = "navColor-".$colorIndex;
				$item->classes[] = $cssClass;

				$colorIndex += 1;

				if($colorIndex > (count($this->_options['colors']) - 1))
					$colorIndex = 0;

				if($this->countMenu) $this->menuCount += 1;
			}

			$this->countMenu = false;
		}

		return $items;
	}

	public function addMenuColors()
	{
		$styles = "";

		foreach($this->_options['colors'] as $key => $val)
		{
			$styles .= "#menu-main .navColor-".$key." a:hover { color: ".$val." !important; border-color: ".$val." !important; } ";
			$styles .= "#menu-main .navColor-".$key.".current_page_item a { color: ".$val." !important; border-color: ".$val." !important; } ";
		}

		echo '<style type="text/css">'.$styles.'</style>';

		//wp_add_inline_style('navColor', $styles);
	}

	public function addAdminScript()
	{
		if(is_admin())
		{
			wp_enqueue_script('navColorizer_script3', SNC_URL . '/js/eye.js');
			wp_enqueue_script('navColorizer_script1', SNC_URL . '/js/utils.js');
			wp_enqueue_script('navColorizer_script2', SNC_URL . '/js/layout.js');
			wp_enqueue_script('navColorizer_script4', SNC_URL . '/js/colorpicker.js');
			wp_enqueue_script('navColorizer_script5', SNC_URL . '/js/navcolorizer.js');

			wp_enqueue_style('navColor-style', SNC_URL . '/css/colorpicker.css');
			wp_enqueue_style('navColor-style', SNC_URL . '/css/layout.css');
		}
	}

	public function init_settings()
	{
		//register_setting('NavColorizer', 'colorcount');
		register_setting('NavColorizer', 'colors');
	}

	public function admin_init()
	{
		$this->init_settings();
		add_action('admin_enqueue_scripts', array(&$this, 'addAdminScript'));
	}

	public function admin_menu()
	{
		add_options_page('Menu Colorizer Plugin', 'Menu Colorizer Plugin', 'manage_options', 'NavColorizer', array(&$this, 'plugin_settings_page'));
	}

	public function plugin_settings_page()
	{
		/*if(!current_user_can('manage_options'))
		{
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}*/
		if(is_admin())
		{
			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}
	}

	public function getSettingsLink($links)
	{
		$settings_link = '<a href="options-general.php?page=NavColorizer">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
}

register_activation_hook( __FILE__, array('NavColorizer', 'install'));
register_deactivation_hook(__FILE__, array('NavColorizer', 'uninstall'));


$pluginRef = NavColorizer::getInstance();
?>