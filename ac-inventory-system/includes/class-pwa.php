<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AC_IS_PWA {

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'add_pwa_meta' ) );
		add_action( 'init', array( __CLASS__, 'handle_pwa_requests' ) );
	}

	public static function add_pwa_meta() {
		echo '<link rel="manifest" href="' . home_url( '/?ac_pwa=manifest' ) . '">' . PHP_EOL;
		echo '<meta name="mobile-web-app-capable" content="yes">' . PHP_EOL;
		echo '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;

		global $wpdb;
		$theme_color = $wpdb->get_var( "SELECT setting_value FROM {$wpdb->prefix}ac_is_settings WHERE setting_key = 'pwa_theme_color'" ) ?: '#2563eb';
		echo '<meta name="theme-color" content="' . esc_attr( $theme_color ) . '">' . PHP_EOL;
		echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . PHP_EOL;

		$icon = $wpdb->get_var( "SELECT setting_value FROM {$wpdb->prefix}ac_is_settings WHERE setting_key = 'pwa_icon_url'" );
		if ( $icon ) {
			echo '<link rel="apple-touch-icon" href="' . esc_url( $icon ) . '">' . PHP_EOL;
		}

		echo '<script>
			if ("serviceWorker" in navigator) {
				window.addEventListener("load", function() {
					navigator.serviceWorker.register("' . home_url( '/?ac_pwa=sw' ) . '").then(function(registration) {
						console.log("AC IS ServiceWorker registration successful with scope: ", registration.scope);
					}, function(err) {
						console.log("AC IS ServiceWorker registration failed: ", err);
					});
				});
			}
		</script>' . PHP_EOL;
	}

	public static function handle_pwa_requests() {
		if ( ! isset( $_GET['ac_pwa'] ) ) {
			return;
		}

		$request = $_GET['ac_pwa'];

		if ( $request === 'manifest' ) {
			self::serve_manifest();
		} elseif ( $request === 'sw' ) {
			self::serve_service_worker();
		}
	}

	private static function serve_manifest() {
		global $wpdb;
		$settings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ac_is_settings", OBJECT_K );

		$manifest = array(
			'name'             => $settings['pwa_app_name']->setting_value ?? 'نظام المبيعات المتطور',
			'short_name'        => $settings['pwa_short_name']->setting_value ?? 'المبيعات',
			'start_url'         => home_url( '/' ),
			'display'           => 'standalone',
			'background_color'  => $settings['pwa_bg_color']->setting_value ?? '#f1f5f9',
			'theme_color'       => $settings['pwa_theme_color']->setting_value ?? '#2563eb',
			'orientation'       => 'any',
			'icons'             => array()
		);

		$icon_url = $settings['pwa_icon_url']->setting_value ?? '';
		if ( $icon_url ) {
			$manifest['icons'][] = array(
				'src'   => $icon_url,
				'sizes' => '512x512',
				'type'  => 'image/png'
			);
			$manifest['icons'][] = array(
				'src'   => $icon_url,
				'sizes' => '192x192',
				'type'  => 'image/png'
			);
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $manifest );
		exit;
	}

	private static function serve_service_worker() {
		header( 'Content-Type: application/javascript' );
		?>
		const CACHE_NAME = 'ac-is-cache-v1';
		const urlsToCache = [
			'/',
			'<?php echo AC_IS_URL . 'assets/css/style-rtl.css'; ?>',
			'<?php echo AC_IS_URL . 'assets/js/scripts.js'; ?>'
		];

		self.addEventListener('install', function(event) {
			event.waitUntil(
				caches.open(CACHE_NAME)
					.then(function(cache) {
						return cache.addAll(urlsToCache);
					})
			);
		});

		self.addEventListener('fetch', function(event) {
			event.respondWith(
				caches.match(event.request)
					.then(function(response) {
						if (response) {
							return response;
						}
						return fetch(event.request);
					}
				)
			);
		});
		<?php
		exit;
	}
}
