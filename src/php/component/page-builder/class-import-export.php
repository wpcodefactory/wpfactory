<?php
/**
 * WPFactory theme - Import Export.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPFactory\WPFactory_Theme\Component\Page_Builder;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper;
use \Timber\Timber;
use WPFactory\WPFactory_Theme\Carbon_Fields\Carbon_Fields_Post_Meta_Datastore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPFactory\WPFactory_Theme\Component\Page_Builder\Import_Export' ) ) {

	class Import_Export {
		function init() {
			// Prevent fields from saving.
			add_filter( 'carbon_fields_should_save_field_value', array( $this, 'prevent_fields_from_saving' ), 10, 3 );
			// Import.
			add_action( 'admin_init', array( $this, 'handle_import' ) );
			// Export.
			add_action( 'admin_init', array( $this, 'handle_export' ) );
		}

		function export_posts_in_json (){

			$args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => -1,
			);

			$query = new \WP_Query( $args );
			$posts = array();

			while( $query->have_posts() ) : $query->the_post();

				$posts[] = array(
					'title' => get_the_title(),
					'excerpt' => get_the_excerpt(),
					'author' => get_the_author()
				);

			endwhile;

			wp_reset_query();

			$data = json_encode($posts);

			// Set appropriate headers for the file download
			header('Content-Description: File Transfer');
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename="data.json"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . strlen($data));

			echo $data;
			exit;

		}

		function handle_export(){
			if (
				isset( $_POST['carbon_fields_compact_input'] ) &&
				isset( $_POST['carbon_fields_compact_input']['_wpft_pb_export'] ) &&
				'yes' === $_POST['carbon_fields_compact_input']['_wpft_pb_export'] &&
				isset( $_POST['carbon_fields_container_wpfactory_settings_nonce'] ) &&
				wp_verify_nonce( $_POST['carbon_fields_container_wpfactory_settings_nonce'], 'carbon_fields_container_wpfactory_settings_nonce' )
			) {
				error_log('----- EXPORT -----');
				//$this->export_posts_in_json();

			}
		}

		function handle_import() {
			if (
				isset( $_POST['carbon_fields_compact_input'] ) &&
				isset( $_POST['carbon_fields_compact_input']['_wpft_pb_import'] ) &&
				'yes' === $_POST['carbon_fields_compact_input']['_wpft_pb_import'] &&
				isset( $_POST['carbon_fields_container_wpfactory_settings_nonce'] ) &&
				wp_verify_nonce( $_POST['carbon_fields_container_wpfactory_settings_nonce'], 'carbon_fields_container_wpfactory_settings_nonce' )
			) {
				$this->delete_import_file();
			}
		}

		function delete_import_file() {
			if ( ! empty( $file_id = get_option( '_wpft_pb_import_file' ) ) ) {
				if ( is_a( wp_delete_attachment( $file_id, true ), 'WP_Post' ) ) {
					update_option( '_wpft_pb_import_file', '' );
				}
			}
		}

		function prevent_fields_from_saving( $save, $value, $field ) {
			if (
				'wpft_pb_import' === $field->get_base_name() ||
				'wpft_pb_export' === $field->get_base_name()
			) {
				$save = false;
			}

			return $save;
		}
	}
}