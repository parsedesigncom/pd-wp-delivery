<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://parsedesign.com
 * @since      1.0.0
 *
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/public
 * @author     Parse Design <info@parsedesign.com>
 */
class Pd_Wp_Delivery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pd_Wp_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pd_Wp_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pd-wp-delivery-public.css', array(), $this->version, 'all' );

	}
	public function login_enqueue_style() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pd_Wp_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pd_Wp_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name.'-login', plugin_dir_url( __FILE__ ) . 'css/pd-wp-delivery-login.css', array(), $this->version, 'all' );

	}

  public function my_wp_logout() {
    wp_safe_redirect( wp_login_url() );
    exit();
  }
  public function my_login_headerurl() {
    return '';
  }
  public function my_login_headertext() {
    return Api_Pd_Wp_Delivery_Env::get( 'D_O__PROJECT_NAME' );
  }

  public function my_template_redirect() {
    // Admin & Login nicht anfassen
    if ( is_admin() ) return;

    if ( defined('DOING_AJAX') && DOING_AJAX ) return;
    if ( defined('REST_REQUEST') && REST_REQUEST ) return;
    if ( defined('DOING_CRON') && DOING_CRON ) return;

    // Login-Seite nicht blockieren
    global $pagenow;
    if ( $pagenow === 'wp-login.php' ) return;

    // Alles andere → Login
    wp_safe_redirect( wp_login_url() );
    exit;
  }

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pd_Wp_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pd_Wp_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pd-wp-delivery-public.js', array( 'jquery' ), $this->version, false );

	}


  public function register_my_post_type() {

    //allergens
    register_post_type( 'allergens', array(
      'labels' => array(
        'name'                     => __( 'Allergene', $this->plugin_name ),
        'singular_name'            => __( 'Allergen', $this->plugin_name ),
        'menu_name'                => __( 'Allergene', $this->plugin_name ),
        'all_items'                => __( 'Alle Allergene', $this->plugin_name ),
        'edit_item'                => __( 'Allergen bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Allergen anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Allergene anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neues Allergen hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neues Allergen', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnetes Allergen:', $this->plugin_name ),
        'search_items'             => __( 'Allergene suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Allergene gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Allergene im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Allergen-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Allergen-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Allergen einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu diesem Allergen hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Allergen-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Allergene nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Allergen-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Allergen-Liste', $this->plugin_name ),
        'item_published'           => __( 'Allergen wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Allergen wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Allergen wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Allergen wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Allergen wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Allergen-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu diesem Allergen.', $this->plugin_name ),
      ),
      'public' => true,
      'exclude_from_search' => true,
      'show_in_rest' => true,
      'menu_icon' => 'dashicons-admin-post',
      'supports' => array(
        0 => 'title',
      ),
      'delete_with_user' => false,
    ) );

    //additives
    register_post_type( 'additives', array(
      'labels' => array(
        'name'                     => __( 'Zusatzstoffe', $this->plugin_name ),
        'singular_name'            => __( 'Zusatzstoffe', $this->plugin_name ),
        'menu_name'                => __( 'Zusatzstoffe', $this->plugin_name ),
        'all_items'                => __( 'Alle Zusatzstoffe', $this->plugin_name ),
        'edit_item'                => __( 'Zusatzstoffe bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Zusatzstoffe anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Zusatzstoffe anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Zusatzstoffe hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Extra', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Zusatzstoffe:', $this->plugin_name ),
        'search_items'             => __( 'Zusatzstoffe suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Zusatzstoffe gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Zusatzstoffe im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Zusatzstoffe-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Zusatzstoffe-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Zusatzstoffe einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Zusatzstoffe hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Zusatzstoffe-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Zusatzstoffe nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Zusatzstoffe-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Zusatzstoffe-Liste', $this->plugin_name ),
        'item_published'           => __( 'Zusatzstoffe wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Zusatzstoffe wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Zusatzstoffe wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Zusatzstoffe wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Zusatzstoffe wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Zusatzstoffe-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Zusatzstoffe.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

    //labeling
    register_post_type( 'labeling', array(
      'labels' => array(
        'name'                     => __( 'Deklarationen', $this->plugin_name ),
        'singular_name'            => __( 'Deklaration', $this->plugin_name ),
        'menu_name'                => __( 'Deklarationen', $this->plugin_name ),
        'all_items'                => __( 'Alle Deklarationen', $this->plugin_name ),
        'edit_item'                => __( 'Deklaration bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Deklaration anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Deklarationen anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Deklaration hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Deklaration', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Deklaration:', $this->plugin_name ),
        'search_items'             => __( 'Deklarationen suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Deklarationen gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Deklarationen im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Deklarationen-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Deklarationen-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Deklaration einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Deklaration hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Deklarationen-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Deklarationen nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Deklarationen-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Deklarationen-Liste', $this->plugin_name ),
        'item_published'           => __( 'Deklaration wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Deklaration wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Deklaration wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Deklaration wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Deklaration wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Deklarations-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Deklaration.', $this->plugin_name ),
      ),
      'public'              => true,
      'exclude_from_search' => true,
      'show_in_rest'        => true,
      'menu_icon'           => 'dashicons-admin-post',
      'supports'            => array( 'title' ),
      'delete_with_user'    => false,
    ) );

    //variant
    register_post_type( 'variant', array(
      'labels' => array(
        'name'                     => __( 'Varianten', $this->plugin_name ),
        'singular_name'            => __( 'Variante', $this->plugin_name ),
        'menu_name'                => __( 'Varianten', $this->plugin_name ),
        'all_items'                => __( 'Alle Varianten', $this->plugin_name ),
        'edit_item'                => __( 'Variante bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Variante anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Varianten anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Variante hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Variante', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Variante:', $this->plugin_name ),
        'search_items'             => __( 'Varianten suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Varianten gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Varianten im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Varianten-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Varianten-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Variante einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Variante hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Varianten-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Varianten nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Varianten-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Varianten-Liste', $this->plugin_name ),
        'item_published'           => __( 'Variante wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Variante wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Variante wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Variante wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Variante wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Varianten-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Variante.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

    //ingredients
    register_post_type( 'ingredients', array(
      'labels' => array(
        'name'                     => __( 'Zutaten', $this->plugin_name ),
        'singular_name'            => __( 'Zutat', $this->plugin_name ),
        'menu_name'                => __( 'Zutaten', $this->plugin_name ),
        'all_items'                => __( 'Alle Zutaten', $this->plugin_name ),
        'edit_item'                => __( 'Zutat bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Zutat anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Zutaten anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Zutat hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Zutat', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Zutat:', $this->plugin_name ),
        'search_items'             => __( 'Zutaten suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Zutaten gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Zutaten im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Zutaten-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Zutaten-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Zutat einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Zutat hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Zutaten-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Zutaten nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Zutaten-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Zutaten-Liste', $this->plugin_name ),
        'item_published'           => __( 'Zutat wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Zutat wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Zutat wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Zutat wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Zutat wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Zutat-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Zutat.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

    //categories
    register_post_type( 'categories', array(
      'labels' => array(
        'name'                     => __( 'Kategorien', $this->plugin_name ),
        'singular_name'            => __( 'Kategorie', $this->plugin_name ),
        'menu_name'                => __( 'Kategorien', $this->plugin_name ),
        'all_items'                => __( 'Alle Kategorien', $this->plugin_name ),
        'edit_item'                => __( 'Kategorie bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Kategorie anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Kategorien anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Kategorie hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Kategorie', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Kategorie:', $this->plugin_name ),
        'search_items'             => __( 'Kategorien suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Kategorien gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Kategorien im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Kategorien-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Kategorien-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Kategorie einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Kategorie hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Kategorien-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Kategorien nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Kategorien-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Kategorien-Liste', $this->plugin_name ),
        'item_published'           => __( 'Kategorie wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Kategorie wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Kategorie wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Kategorie wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Kategorie wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Kategorie-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Kategorie.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

    //product
    register_post_type( 'product', array(
      'labels' => array(
        'name'                     => __( 'Produkte', $this->plugin_name ),
        'singular_name'            => __( 'Produkt', $this->plugin_name ),
        'menu_name'                => __( 'Produkte', $this->plugin_name ),
        'all_items'                => __( 'Alle Produkte', $this->plugin_name ),
        'edit_item'                => __( 'Produkt bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Produkt anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Produkte anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neues Produkt hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neues Produkt', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnetes Produkt:', $this->plugin_name ),
        'search_items'             => __( 'Produkte suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Produkte gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Produkte im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Produkt-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Produkt-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Produkt einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu diesem Produkt hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Produkte-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Produkte nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Produkte-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Produkte-Liste', $this->plugin_name ),
        'item_published'           => __( 'Produkt wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Produkt wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Produkt wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Produkt wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Produkt wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Produkt-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu diesem Produkt.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

    //orders
    register_post_type( 'orders', array(
      'labels' => array(
        'name'                     => __( 'Bestellungen', $this->plugin_name ),
        'singular_name'            => __( 'Bestellung', $this->plugin_name ),
        'menu_name'                => __( 'Bestellungen', $this->plugin_name ),
        'all_items'                => __( 'Alle Bestellungen', $this->plugin_name ),
        'edit_item'                => __( 'Bestellung bearbeiten', $this->plugin_name ),
        'view_item'                => __( 'Bestellung anzeigen', $this->plugin_name ),
        'view_items'               => __( 'Bestellungen anzeigen', $this->plugin_name ),
        'add_new_item'             => __( 'Neue Bestellung hinzufügen', $this->plugin_name ),
        'add_new'                  => __( 'Neu hinzufügen', $this->plugin_name ),
        'new_item'                 => __( 'Neue Bestellung', $this->plugin_name ),
        'parent_item_colon'        => __( 'Übergeordnete Bestellung:', $this->plugin_name ),
        'search_items'             => __( 'Bestellungen suchen', $this->plugin_name ),
        'not_found'                => __( 'Keine Bestellungen gefunden', $this->plugin_name ),
        'not_found_in_trash'       => __( 'Keine Bestellungen im Papierkorb gefunden', $this->plugin_name ),
        'archives'                 => __( 'Bestellungs-Archiv', $this->plugin_name ),
        'attributes'               => __( 'Bestellungs-Attribute', $this->plugin_name ),
        'insert_into_item'         => __( 'In Bestellung einfügen', $this->plugin_name ),
        'uploaded_to_this_item'    => __( 'Zu dieser Bestellung hochgeladen', $this->plugin_name ),
        'filter_items_list'        => __( 'Bestellungen-Liste filtern', $this->plugin_name ),
        'filter_by_date'           => __( 'Bestellungen nach Datum filtern', $this->plugin_name ),
        'items_list_navigation'    => __( 'Bestellungen-Listen-Navigation', $this->plugin_name ),
        'items_list'               => __( 'Bestellungen-Liste', $this->plugin_name ),
        'item_published'           => __( 'Bestellung wurde veröffentlicht.', $this->plugin_name ),
        'item_published_privately' => __( 'Bestellung wurde privat veröffentlicht.', $this->plugin_name ),
        'item_reverted_to_draft'   => __( 'Bestellung wurde auf Entwurf zurückgesetzt.', $this->plugin_name ),
        'item_scheduled'           => __( 'Bestellung wurde geplant.', $this->plugin_name ),
        'item_updated'             => __( 'Bestellung wurde aktualisiert.', $this->plugin_name ),
        'item_link'                => __( 'Bestellungs-Link', $this->plugin_name ),
        'item_link_description'    => __( 'Ein Link zu dieser Bestellung.', $this->plugin_name ),
      ),
      'public'           => true,
      'show_in_rest'     => true,
      'menu_icon'        => 'dashicons-admin-post',
      'supports'         => array( 'title' ),
      'delete_with_user' => false,
    ) );

  }

}
