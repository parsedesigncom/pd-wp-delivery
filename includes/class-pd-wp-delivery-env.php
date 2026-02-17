<?php

/**
 * Loads and manages environment variables from the .env file.
 *
 * @package    Api_Smart_Web_Academy
 * @subpackage Api_Smart_Web_Academy/includes
 */
class Api_Pd_Wp_Delivery_Env {

	/**
	 * Stored environment variables
	 *
	 * @var array
	 */
	private static $env_vars = array();

	/**
	 * Whether the .env has been loaded
	 *
	 * @var bool
	 */
	private static $loaded = false;

	/**
	 * Loads the .env file
	 *
	 * @param string $path Path to the .env file
	 * @return bool True if successfully loaded
	 */
	public static function load( $path = null ) {
		if ( self::$loaded ) {
			return true;
		}

		if ( null === $path ) {
			$path = plugin_dir_path( dirname( __FILE__ ) ) . '.env';
		}

		if ( ! file_exists( $path ) ) {
			return false;
		}

		$lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		if ( false === $lines ) {
			return false;
		}

		foreach ( $lines as $line ) {
			// Skip comments
			$line = trim( $line );
			if ( empty( $line ) || 0 === strpos( $line, '#' ) ) {
				continue;
			}

			// Parse KEY=VALUE
			$parts = explode( '=', $line, 2 );
			if ( count( $parts ) !== 2 ) {
				continue;
			}

			$key   = trim( $parts[0] );
			$value = trim( $parts[1] );

			// Remove quotes
			$value = trim( $value, '"' );
			$value = trim( $value, "'" );

			self::$env_vars[ $key ] = $value;
		}

		self::$loaded = true;
		return true;
	}

	/**
	 * Gets a value from the .env file
	 *
	 * @param string $key The key
	 * @param mixed $default Default value if not found
	 * @return mixed The value or default
	 */
	public static function get( $key, $default = null ) {
		if ( ! self::$loaded ) {
			self::load();
		}

		if ( isset( self::$env_vars[ $key ] ) ) {
			return self::$env_vars[ $key ];
		}

		return $default;
	}

	/**
	 * Checks if a key exists
	 *
	 * @param string $key The key
	 * @return bool
	 */
	public static function has( $key ) {
		if ( ! self::$loaded ) {
			self::load();
		}

		return isset( self::$env_vars[ $key ] );
	}

	/**
	 * Returns all loaded variables
	 *
	 * @return array
	 */
	public static function all() {
		if ( ! self::$loaded ) {
			self::load();
		}

		return self::$env_vars;
	}
}