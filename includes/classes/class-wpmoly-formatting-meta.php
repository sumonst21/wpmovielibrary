<?php
/**
 * WPMovieLibrary Utils Formatting_Meta Class extension.
 * 
 * This class contains only Metadata formatting methods.
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Formatting_Meta' ) ) :

	class WPMOLY_Formatting_Meta {

		public static $priority = 10;

		/**
		 * Format a Movie's director for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_director( $data ) {

			$output = WPMOLY_Utils::format_movie_terms_list( $data, 'collection' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's genres for display
		 * 
		 * Match each genre against the genre taxonomy to detect missing
		 * terms. If term genre exists, provide a link, raw text value
		 * if no matching term could be found.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_genres( $data ) {

			$output = WPMOLY_Utils::format_movie_terms_list( $data, 'genre' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's casting for display
		 * 
		 * Match each actor against the actor taxonomy to detect missing
		 * terms. If term actor exists, provide a link, raw text value
		 * if no matching term could be found.
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_cast( $data ) {

			$output = WPMOLY_Utils::format_movie_terms_list( $data,  'actor' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's release date for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_release_date( $data, $format = null ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			if ( is_null( $format ) )
				$format = wpmoly_o( 'format-date' );

			if ( '' == $format )
				$format = 'j F Y';

			$output = date_i18n( $format, strtotime( $data ) );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's runtime for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_runtime( $data, $format = null ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			if ( '0' == $data )
				return self::format_movie_field( __( 'Duration unknown', 'wpmovielibrary' ) );

			if ( is_null( $format ) )
				$format = wpmoly_o( 'format-time' );

			if ( '' == $format )
				$format = 'G \h i \m\i\n';

			$output = date_i18n( $format, mktime( 0, $data ) );
			if ( false !== stripos( $output, 'am' ) || false !== stripos( $output, 'pm' ) )
				$output = date_i18n( 'G:i', mktime( 0, $data ) );

			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's languages for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_spoken_languages( $data ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			$data = explode( ',', $data );
			foreach ( $data as $i => $d ) {

				$d = trim( $d );
				if ( '1' == wpmoly_o( 'translate-languages' ) ) {
					$title = WPMOLY_L10n::get_language_standard_name( $d );
					$title = __( $title, 'wpmovielibrary-iso' );
				} else {
					$title = $d;
				}

				$url = apply_filters( 'wpmoly_movie_meta_link', 'spoken_languages', $d, 'meta', $title );
				$data[ $i ] = $url;
			}

			$data = implode( ', ', $data );
			$output = self::format_movie_field( $data );

			return $output;
		}

		/**
		 * Format a Movie's countries for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_production_countries( $data ) {

			if ( is_null( $data ) || '' == $data )
				return $data;

			if ( '1' == wpmoly_o( 'translate-countries' ) ) {
				$format = wpmoly_o( 'countries-format', array() );
			} else {
				$format = array( 'flag', 'original' );
			}

			$data = explode( ',', $data );
			foreach ( $data as $i => $country ) {

				$country = trim( $country );
				$value   = $country;
				$_value  = array();

				foreach ( $format as $c => $f ) {

					switch ( $f ) {
						case 'flag':
							$country = WPMOLY_L10n::get_country_standard_name( $country );
							$code    = WPMOLY_L10n::get_country_code( $country );
							$text = self::movie_country_flag( $code, $country );
							break;
						case 'original':
							$text = $country;
							break;
						case 'translated':
							$country = WPMOLY_L10n::get_country_standard_name( $country );
							$country = __( $country, 'wpmovielibrary-iso' );
							$text = $country;
							break;
						case 'ptranslated':
							$country = __( $country, 'wpmovielibrary-iso' );
							$text = sprintf( '(%s)', $country );
							break;
						case 'poriginal':
							$country = WPMOLY_L10n::get_country_standard_name( $country );
							$text = sprintf( '(%s)', $country );
							break;
						default:
							$text = '';
							break;
					}

					if ( 'flag' != $f && '' != $text ) {
						$text = apply_filters( 'wpmoly_movie_meta_link', 'production_countries', $value, 'meta', $text );
					}

					$_value[] = $text;
				}

				$data[ $i ] = implode( '&nbsp;',$_value );
			}

			$data = implode( ',&nbsp; ', $data );
			$output = self::format_movie_field( $data );

			return $output;
		}

		/**
		 * Format a Movie's production companies for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_production_companies( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'production_companies', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's producer for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_producer( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'producer', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's composer for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_composer( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'composer', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's editor for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_editor( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'editor', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's author for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_author( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'author', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's director of photography for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_photography( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'photography', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's certification for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_certification( $data ) {

			$output = apply_filters( 'wpmoly_movie_meta_link', 'certification', $data, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's budget for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_budget( $data, $format = 'html' ) {

			$output = intval( $data );
			if ( ! $output )
				return $output;

			if ( 'html' != $format )
				$format = 'raw';

			if ( 'html' == $format )
				$output = '$' . number_format_i18n( $output );

			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's revenue for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_revenue( $data, $format = 'html' ) {

			$output = intval( $data );
			if ( ! $output )
				return $output;

			if ( 'html' != $format )
				$format = 'raw';

			if ( 'html' == $format )
				$output = '$' . number_format_i18n( $output );

			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format a Movie's adult status for display
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_adult( $data ) {

			$status = array( 'true', 'false' );
			$output = array( __( 'Yes', 'wpmovielibrary' ), __( 'No', 'wpmovielibrary' ) );

			$output = str_replace( $status, $output, $data );
			$output = apply_filters( 'wpmoly_movie_meta_link', 'adult', $output, 'meta' );
			$output = self::format_movie_field( $output );

			return $output;
		}

		/**
		 * Format movie homepage link.
		 * 
		 * @since    2.0.3
		 * 
		 * @param    string    $data Homepage link
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_homepage( $data ) {

			if ( '' != $data )
				$data = sprintf( '<a href="%1$s" title="%2$s">%1$s</a>', $data, __( 'Official Website', 'wpmovielibrary' ) );

			$output = self::format_movie_field( $data );

			return $output;
		}

		/**
		 * Format a Movie's misc field for display
		 * 
		 * @since    1.1
		 * 
		 * @param    string    $data field value
		 * 
		 * @return   string    Formatted output
		 */
		public static function format_movie_field( $data ) {

			if ( '' == $data )
				$data = '&mdash;';

			return $data;
		}

		/**
		 * Add tiny flags before country names.
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $code Country ISO code
		 * @param    string    $name Country nam
		 * 
		 * @return   string    Formatted output
		 */
		public static function movie_country_flag( $code, $name ) {

			if ( ! in_array( 'flag', wpmoly_o( 'countries-format' ) ) )
				return $name;

			$flag = '<span class="flag flag-%s" title="%s"></span>';
			$flag = sprintf( $flag, strtolower( $code ), $name );

			/**
			 * Apply filter to the rendered country flag
			 * 
			 * @since    2.0
			 * 
			 * @param    string    $flag HTML markup
			 * @param    string    $code Country ISO code
			 * @param    string    $name Country name
			 */
			$flag = apply_filters( 'wpmoly_filter_country_flag_html', $flag, $code, $name );

			return $flag;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Aliases
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		public static function format_movie_actors( $data ) {
			return self::format_movie_cast( $data );
		}

		public static function format_movie_date( $data, $format = null ) {
			return self::format_movie_release_date( $data, $format );
		}

		public static function format_movie_local_release_date( $data, $format = null ) {
			return self::format_movie_release_date( $data, $format );
		}

		public static function format_movie_year( $data ) {
			return self::format_movie_release_date( $data, $format = 'Y' );
		}

		public static function format_movie_languages( $data ) {
			return self::format_movie_spoken_languages( $data );
		}

		public static function format_movie_countries( $data ) {
			return self::format_movie_production_countries( $data );
		}

		public static function format_movie_production( $data ) {
			return self::format_movie_production_companies( $data );
		}

	}

endif;
