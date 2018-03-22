<?php
class SWOTCM_Public
{
	protected $tablename;
	/**
	* Start up
	*/
	public function __construct(){
		global $wpdb;
        $this->tablename = $wpdb->prefix . "swotcm_campaigns";

		add_shortcode( 'sw-codemanager',array( $this,'swotcm_shortcode_function' ) );
	}

	/**
	* Shortcode function
	* Sample: [sw-codemanager campaign="6921f1" display="code" message="Out of Code Custom Message Goes here"]
	*/
	public function swotcm_shortcode_function( $atts ){
		global $wpdb;
		$args = shortcode_atts( array(
		'campaign' => '',
		'display' => '',
		'message' => ''
		), $atts );
		
		ob_start();
		
		$campaign_id = $args['campaign'];
		$display = $args['display'];
		$message = $args['message'];

		if ( isset( $_COOKIE['CID_'.$campaign_id] ) ) {

			$existing_code =  $_COOKIE['CID_'.$campaign_id];
			$existing_code_exp = explode( '|', $existing_code );
			$couponCode = $existing_code_exp[0];
			$couponURL = $existing_code_exp[1];
			$couponMessage = $existing_code_exp[2];

			if ( $display == 'code' ){
				if ( $couponCode ){
					echo $couponCode;
				}else{
					if ( $message  ){
						echo $message;
					}else{
						echo $couponMessage;
					}
				}
			}elseif ( $display == 'url' ){
				echo $couponURL;
			}

		} else {

			$swotcm_api_option = get_option( 'swotcm_admin_option' );
			$swotcm_auth_key = $swotcm_api_option['swotcm_auth_key'];

			$sql_query = $wpdb->get_results( "SELECT `extid`,`u_status`  FROM $this->tablename WHERE `shortcode` LIKE '$campaign_id'" );
			$extid = $sql_query[0]->extid;
			$u_status = $sql_query[0]->u_status;

			if ( '1' == $u_status ) {

				$service_url = "http://sellerwidgets.com/api/v1/client/campaigns/".$extid."/code";

				$curl = curl_init();

				curl_setopt_array( $curl, array(
					CURLOPT_URL => $service_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "PATCH",
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
				$coupon_code = $decoded['code'];
				$coupon_url = $decoded['url'];
				$coupon_message = $decoded['message'];

				if ( $display == 'code' ){
					if ( $coupon_code ){
						echo $coupon_code;
					}else{
						if ( $message  ){
							echo $message;
						}else{
							echo $coupon_message;
						}
					}
				}elseif ( $display == 'url' ){
					echo $coupon_url;
				}
				?>
				<script type="text/javascript">
					createCookie('CID_<?php echo $campaign_id ?>', '<?php echo esc_html($coupon_code) ?>|<?php echo esc_url($coupon_url); ?>|<?php echo esc_html($coupon_message); ?>', 30);
					function createCookie(name, value, days) {
						var expires = "";
						if (days) {
							var date = new Date();
							date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
							expires = "; expires=" + date.toUTCString();
						}
						document.cookie = name + "=" + value + expires + "; path=/";
					}
				</script>
				<?php
				}
			}else{
				echo 'Camapign Inactive!';
			}
		}

		return ob_get_clean();
	}

}
$swotcm_public = new SWOTCM_Public();