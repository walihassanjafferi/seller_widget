<?php
class Seller_Widgets_OTCM {

	/**
	 * Represents the slug of hte plugin that can be used throughout the plugin
	 * for internationalization and other purposes.
	 *
	 * @access protected
	 * @var    string   $plugin_slug    The single, hyphenated string used to identify this plugin.
	 */
	protected $plugin_slug;


	public function __construct() {

		$this->plugin_slug = 'swotcm-slug';

		$this->load_dependencies();

	}

	private function load_dependencies() {

		require_once( SW_OTCM_PATH . 'classes/admin/class-swotcm-admin.php' );
		require_once( SW_OTCM_PATH . 'classes/admin/class-swotcm-campaigns.php' );
		require_once( SW_OTCM_PATH . 'classes/public/class-swotcm-public.php' );
	}

    public static function create_SWOTCM_db() {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'swotcm_campaigns';

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name varchar(255) NULL,
            extid varchar(100) NOT NULL,
            u_status varchar(255) NULL,
            c_status varchar(255) NULL,
            shortcode varchar(255) NULL,
            message text(1000) NULL,
            UNIQUE KEY  extid ( extid )
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}