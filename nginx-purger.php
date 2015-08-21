<?php
/*
Plugin Name: Nginx Purger
Version: 0.1.0
Text Domain: nginx-purger
Domain Path: /languages
*/

add_action( 'admin_menu', function(){
	add_options_page( 'Nginx Purger', 'Nginx Purger', 'publish_pages', 'nginx-purger', function(){
		?>
			<style>
				#nginx-purger-logs
				{
					background-color: #ffffff;
					padding: 20px;
				}
				.nginx-purger-log
				{
					border: 1px solid #dedede;
					margin-bottom: 10px;
				}
				.nginx-purger-url
				{
					border-bottom: 1px solid #dedede;
					padding: 10px;
					border-left: 10px solid rgb( 208,232,246 );
				}
				.nginx-purger-url.nginx-purger-error
				{
					border: 1px solid rgb( 213, 37, 36 );
					border-left: 10px solid rgb( 213, 37, 36 );
				}
				.nginx-purger-result
				{
					padding: 10px 10px 10px 20px;
				}
			</style>
			<div class="wrap">
				<h1>Nginx Purger</h1>
				<form method="post">
					<?php wp_nonce_field( 'nginx-purger', 'nginx-purger-nonce' ); ?>
					<p><input type="text" name="url" value="" style="width: 100%; max-width: 500px; height: 40px; line-height: 40px;" /></p>
					<p><input type="submit" name="submit" id="submit" class="button-primary" value="Submit"></p>
				</form>
				<div id="nginx-purger-logs">
					<?php
						$logs = get_option( 'nginx-purger-log', array() );
						foreach ( $logs as $log ) {
							?>
								<div class="nginx-purger-log">
									<?php if ( is_wp_error( $log['result'] ) ): ?>
										<div class="nginx-purger-url nginx-purger-error"><?php echo esc_url( $log['url'] ); ?></div>
									<?php else: ?>
										<div class="nginx-purger-url"><?php echo esc_url( $log['url'] ); ?></div>
									<?php endif; ?>
									<?php if ( is_wp_error( $log['result'] ) ): ?>
										<div class="nginx-purger-result"><?php echo esc_html( $log['result']->get_error_code() ); ?>: <?php echo esc_html( $log['result']->get_error_message() ); ?></div>
									<?php else: ?>
										<div class="nginx-purger-result"><?php echo esc_html( $log['result']['body'] ); ?></div>
									<?php endif; ?>
								</div>
							<?php
						}
					?>
				</div>
			</div>
		<?php
	} );
} );

add_action( 'admin_init', function(){
	if ( ! empty( $_POST['nginx-purger-nonce'] ) && wp_verify_nonce( $_POST['nginx-purger-nonce'], 'nginx-purger' )  ) {
		if ( ! empty( $_POST['nginx-purger-nonce'] ) && $_POST['url'] ) {
			update_option( 'nginx-purger-log', nginx_purger_purge( $_POST['url'] ) );
			wp_safe_redirect( admin_url( 'options-general.php?page=nginx-purger' ) );
		}
	}
} );

function nginx_purger_purge( $url ) {
	global $nginx_servers;
	$logs = array();
	if ( isset( $nginx_servers ) && is_array( $nginx_servers ) ) {
		foreach ( $nginx_servers as $server ) {
			$req = nginx_purger_get_purger_url( $server, $url );
			$res = nginx_purger_send_request( $req );
			$logs[] = array( 'url' => $req, 'result' => $res );
		}
	}
	return $logs;
}

function nginx_purger_send_request( $req ) {
	$res = wp_remote_get( $req );
	if ( ! is_wp_error( $res ) && 200 !== $res['response']['code'] ) {
		$res = new WP_Error( $res['response']['code'], $res['response']['message'] );
	}
	return $res;
}

function nginx_purger_get_purger_url( $server, $url ) {
	$url = nginx_purger_remove_scheme_from_url( $url );
	return untrailingslashit( esc_url_raw( $server, array( 'http', 'https' ) ) ) . '/purge/' . $url;
}

function nginx_purger_remove_scheme_from_url( $url ) {
	$url = esc_url_raw( $url, array( 'http', 'https' ) );
	return preg_replace( '#^https?://#', '', $url );
}
