<?php
class SWOTCM_Settings_Page
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    protected $tablename;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'swotcm_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'swotcm_page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'swotcm_scripts_admin' ) );

        global $wpdb;
        $this->tablename = $wpdb->prefix . "swotcm_campaigns";        
    }

    // Enqueue Plugin's Admin Scripts
    public function swotcm_scripts_admin() {
        //Load all JS
        wp_enqueue_script( 'swotcm-admin-jquery', '//code.jquery.com/jquery-1.12.4.min.js');
        wp_enqueue_script( 'swotcm-admin-datatables', '//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js');
        wp_enqueue_script( 'swotcm-admin-datatables-ui', '//cdn.datatables.net/1.10.16/js/dataTables.jqueryui.min.js');
        //Load all CSS
        wp_enqueue_style( 'swotcm-admin-jquery-theme', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style( 'swotcm-admin-jquery-datatables', '//cdn.datatables.net/1.10.16/css/dataTables.jqueryui.min.css');
        wp_enqueue_style( 'swotcm-admin', SW_OTCM_DIR_PATH . 'css/swotcm_admin.css');
        wp_enqueue_script( 'swotcm-admin-js', SW_OTCM_DIR_PATH . 'js/swotcm_admin.js');
    }

    /**
     * Add options page
     */
    public function swotcm_plugin_page()
    {
        add_menu_page( 'Seller Widgets', 'Seller Widgets OTCM', 'manage_options', 'seller_widgets' );
        add_submenu_page( 'seller_widgets', 'Seller Widgets OTCM', 'SWOTCM Settings', 'manage_options', 'seller_widgets', array($this,'create_admin_page') );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'swotcm_admin_option' );
        ?>
        <div class="swotcm_wrap">
            <div style="float:right;text-align:center;">

                <a href="https://sellerwidgets.com">Https://SellerWidgets.com</a>
                <br/>
                <a href="https://sellerwidgets.com/support"><img src="<?php echo SW_OTCM_DIR_PATH; ?>/images/question.png'" width="100px;"></a>

            </div>
            <h1>Seller Widgets</h1>
                  
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'swotcm_admin_option_group' );
                do_settings_sections( 'swotcm-setting-admin' );
                submit_button('Authorize');
            ?>
            <div style="float:right;">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Refresh">
            </div>
            </form>

            <?php 
            if ( isset( $this->options['swotcm_auth_key'] ) || isset($_POST['refresh']) )
            {
                $campaigns = new SWOTCM_Campaigns();
                $campaigns->render_camapigns();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function swotcm_page_init()
    {        
        register_setting(
            'swotcm_admin_option_group', // Option group
            'swotcm_admin_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'SWOTCM Settings', // Title
            '',
            'swotcm-setting-admin' // Page
        );  

        add_settings_field(
            'swotcm_auth_key', // ID
            'Authorization Key', // Title 
            array( $this, 'authorization_key_callback' ), // Callback
            'swotcm-setting-admin', // Page
            'setting_section_id' // Section           
        );      
    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['swotcm_auth_key'] ) )
            $new_input['swotcm_auth_key'] = esc_attr( $input['swotcm_auth_key'] );

        return $new_input;
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function authorization_key_callback()
    {
        printf(
            '<input type="text" id="swotcm_auth_key" name="swotcm_admin_option[swotcm_auth_key]" value="%s" size="50"/>',
            isset( $this->options['swotcm_auth_key'] ) ? esc_attr( $this->options['swotcm_auth_key']) : ''
        );
    }

}

if( is_admin() )
    $swotcm_settings_page = new SWOTCM_Settings_Page();
