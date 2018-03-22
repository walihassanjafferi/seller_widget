<?php
class SWOTCM_Campaigns
{
    protected $tablename;
    /**
    * Start up
    */
    public function __construct()
    {
        global $wpdb;
        $this->tablename = $wpdb->prefix . "swotcm_campaigns";

    }

    /** 
    * Print the Section text
    */
    public function render_camapigns()
    {
        ini_set( 'max_execution_time', 300 ); 

        $swotcm_api_option = get_option( 'swotcm_admin_option' );
        $swotcm_auth_key = $swotcm_api_option['swotcm_auth_key'];

        $service_url = 'http://sellerwidgets.com/api/v1/client/campaigns';

        $curl = curl_init();

        curl_setopt_array( $curl, array(
          CURLOPT_URL => $service_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "client-id-api-key: $swotcm_auth_key",
          ),
        ));

        $response = curl_exec( $curl );

        $err = curl_error( $curl );

        curl_close( $curl );

        if ( $err ) {

          echo "cURL Error #:" . $err;

        } else {

        $decoded = json_decode( $response, true );

        if ( isset( $decoded->response->status ) && $decoded->response->status == 'ERROR' ) {
            die( 'error occured: ' . $decoded->response->errormessage );
        }
            
                $this->save_to_db( $decoded );

            if ( isset( $_GET['action'] ) && $_GET['action'] == 'status_change'){

                global $wpdb;

                    $ext_id = $_GET['eid'];

                    $u_status = $_GET['status'];

                    $wpdb->query( $wpdb->prepare( "UPDATE $this->tablename SET u_status = %s WHERE extid = %s",$u_status, $ext_id ) );
                    
                    $this->render_table();

            }else{                  
                $this->render_table();
            }
        }
    }
    /** 
    * Print the Section text
    */
    public function save_to_db( $decoded )
    {
        global $wpdb;
        foreach ( $decoded as $row ){
            $name     = $row['name']; //string value use: %s
            $extid    = $row['ext_id']; //string value use: %s
            $u_status = "0"; //numeric value use: %d
            $c_status = $row['status']; //string value use: %s
            $shortcode= $row['short_code']; //string value use: %s
            $message  = $row['out_of_codes_message']; //string value use: %s

            $sql = $wpdb->prepare("INSERT INTO `$this->tablename` (`id`, `name`, `extid`, `u_status`, `c_status`, `shortcode`, `message`) values (NULL, %s, %s, %s, %s, %s, %s)", $name, $extid, $u_status, $c_status, $shortcode, $message);

            $wpdb->query($sql);
        }

    }
    /** 
    * Print the Section text
    */
    public function render_table()
    {
        global $wpdb;  
        $sql_query = 
        $results = $wpdb->get_results( 'SELECT * FROM '.$this->tablename, ARRAY_A );

        ?>
        <table id="campaigns" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Active</th>
                    <th>Campaign Name</th>
                    <th>Slug</th>
                    <th>Out of Code Message</th>
                    <th>CODE Shortcode</th>
                    <th>URL Shortcode</th>
                </tr>
        </thead>
        <?php
        $counter = 1;
        foreach ( $results as $row ){
            if ( '0' == $row['u_status'] ){
                $row_status = '0';
                $new_status = '1';                
            }else{
                $row_status = '1';
                $new_status = '0';
            }
            $ext_id = $row['extid'];
            echo "<tr>";
            ?>
            <td>
                <input type='checkbox' id='condition' name='condition' onchange="window.location.href='<?php echo esc_url ( admin_url() ); ?>admin.php?page=seller_widgets&action=status_change&status=<?php echo $new_status; ?>&eid=<?php echo $ext_id; ?>'"  <?php echo ( $row_status == '1' ) ? 'checked' : ''; ?>>
            </td>
            <?php
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['shortcode']."</td>";
            echo "<td>".$row['message']."</td>";
            ?>
            <td>
                <p id="s_code_<?php echo $counter; ?>" style="display:none;">[sw-codemanager campaign="<?php echo $row['shortcode'];?>" display="code" ]</p>
                    <button onclick="copyToClipboard('#s_code_<?php echo $counter; ?>')">Copy to Clipboard</button>
            </td>
            <td>
                <p id="s_url_<?php echo $counter; ?>" style="display:none;">[sw-codemanager campaign="<?php echo $row['shortcode'];?>" display="url" ]</p>
                    <button onclick="copyToClipboard('#s_url_<?php echo $counter; ?>')">Copy to Clipboard</button>
            </td>            
            <?php
            $counter++;
        }
        ?>
    </table>
     
    <?php
    }
}
if( is_admin() )
    $swotcm_campaigns = new SWOTCM_Campaigns();
