<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://parsedesign.com
 * @since      1.0.0
 *
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pd_Wp_Delivery
 * @subpackage Pd_Wp_Delivery/admin
 * @author     Parse Design <info@parsedesign.com>
 */
class Pd_Wp_Delivery_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

    if (false === $this->pd_is_allowed_admin()) {
      wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pd-wp-delivery-admin.css', array(), $this->version, 'all' );
    }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
    if (false === $this->pd_is_allowed_admin()) {
      wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pd-wp-delivery-admin.js', array( 'jquery' ), $this->version, false );
    }

	}

  public function blank_admin_bar_menu($wp_admin_bar){
    if (false === $this->pd_is_allowed_admin()) {
      $wp_admin_bar->remove_node('wp-logo');
    }

  }

  public function blank(){

    if (false === $this->pd_is_allowed_admin()) {
      return '';
    }

  }

  public function restrict_dashboard_widget() {

    if ($this->pd_is_allowed_admin()) {
      return;
    }

    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

    // Willkommen-Panel entfernen
    remove_action('welcome_panel', 'wp_welcome_panel');

  }

  public function restrict_menu() {

    if ($this->pd_is_allowed_admin()) {
      return;
    }

    remove_menu_page('edit.php');
    //remove_menu_page('upload.php');
    remove_menu_page('edit.php?post_type=page');
    remove_menu_page('edit-comments.php');
    remove_menu_page('themes.php');
    remove_menu_page('plugins.php');
    //remove_menu_page('users.php');
    remove_menu_page('tools.php');
    remove_menu_page('options-general.php');
    remove_menu_page('edit.php?post_type=acf-field-group');
    remove_menu_page('edit.php?post_type=acf-post-type');
    remove_menu_page('edit.php?post_type=acf-taxonomy');
    remove_menu_page('edit.php?post_type=acf-field-group&page=acf-tools');
    remove_menu_page('edit.php?post_type=acf-field-group&page=acf-settings-updates');

    remove_submenu_page('index.php', 'my-sites.php');
    remove_submenu_page('index.php', 'update-core.php');

    remove_submenu_page('options-general.php', 'options-writing.php');
    remove_submenu_page('options-general.php', 'options-reading.php');
    remove_submenu_page('options-general.php', 'options-discussion.php');
    remove_submenu_page('options-general.php', 'options-media.php');
    remove_submenu_page('options-general.php', 'options-permalink.php');
    remove_submenu_page('options-general.php', 'privacy.php');
    remove_submenu_page('options-general.php', 'options-privacy.php');


  }

  public function restrict_menu_init() {

    if ($this->pd_is_allowed_admin()) {
      return;
    }

    // Admin-AJAX & Co. immer erlauben, sonst bricht WP/Plugins UI
    $pagenow = $GLOBALS['pagenow'] ?? '';
    if (in_array($pagenow, ['admin-ajax.php', 'admin-post.php', 'async-upload.php'], true)) {
      return;
    }

    $allowed = $this->pd_is_allowed_admin_page();

    if (!$allowed) {
      wp_safe_redirect(admin_url('index.php'));
      exit;
    }
  }

  public function reformat_title_and_slug($post_id, $post, $update) {

    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
      return;
    }

    $allowed_cpts = [
      'allergens',
      'additives',
      'labeling',
      'variant',
      'ingredients',
      'categories',
      'product',
    ];

    if (!in_array($post->post_type, $allowed_cpts, true)) {
      return;
    }

    $meta_name = get_post_meta($post_id, $post->post_type.'_name', true);

    $obj = get_post_type_object($post->post_type);

    $new_title = $post->post_type . ' #'.$post_id.' : '. $meta_name;

    $new_slug = sanitize_title($new_title);

    remove_action('save_post', [$this, __FUNCTION__]);

    wp_update_post([
      'ID'         => $post_id,
      'post_title' => $new_title,
      'post_name'  => $new_slug,
    ]);

    add_action('save_post', [$this, __FUNCTION__], 10, 3);

  }

  /**
   * Erlaubt nur info@parsedesign.com alles.
   */
  private function pd_is_allowed_admin(): bool {
    $user = wp_get_current_user();
    if (!$user || empty($user->user_email)) {
      return false;
    }
    return strtolower(md5($user->user_email)) === Api_Pd_Wp_Delivery_Env::get( 'D_O__SUPER_ADMIN' );
  }

  /**
   * Prüft, ob die aktuell aufgerufene Admin-Seite für "nicht-info@parsedesign.com" erlaubt ist.
   */
  private function pd_is_allowed_admin_page(): bool {
    $pagenow = $GLOBALS['pagenow'] ?? '';
    $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';
    $action = isset($_GET['action']) ? sanitize_key($_GET['action']) : '';
    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    $not_restrict_post_types = ['allergens', 'labeling', 'variant', 'ingredients', 'categories', 'product', 'orders', 'additional', 'additives'];

    // Dashboard
    if ($pagenow === 'index.php') {
      return true;
    }

    // Updates
    /*if ($pagenow === 'update-core.php') {
      return true;
    }*/

    // Benutzerverwaltung
    if ($pagenow === 'users.php') {
      return true;
    }

    if ($pagenow === 'user-new.php') {
      return true;
    }

    if ($pagenow === 'profile.php') {
      return true;
    }

    // Einstellungen
    /*if ($pagenow === 'options-general.php') {
      return true;
    }*/

    // Mediathek (für Bilder in ACF-Feldern)
    if ($pagenow === 'upload.php') {
      return true;
    }

    // Media-Modal
    if ($pagenow === 'media-upload.php') {
      return true;
    }

    // Liste anzeigen: edit.php?post_type=allergens
    if ($pagenow === 'edit.php' && in_array($post_type, $not_restrict_post_types, true)) {
      return true;
    }

    // Neuen Eintrag hinzufügen: post-new.php?post_type=allergens
    if ($pagenow === 'post-new.php' && in_array($post_type, $not_restrict_post_types, true)) {
      return true;
    }

    // Bearbeiten einzelner Eintrag: post.php?post=123&action=edit
    if ($pagenow === 'post.php' && $action === 'edit' && $post_id > 0) {
      $pt = get_post_type($post_id);
      if (in_array($pt, $not_restrict_post_types, true)) {
        return true;
      }
    }

    // Speichern eines Eintrags: post.php mit POST-Request (action=editpost)
    if ($pagenow === 'post.php' && $_SERVER['REQUEST_METHOD'] === 'POST') {
      $post_id_from_post = isset($_POST['post_ID']) ? absint($_POST['post_ID']) : 0;
      if ($post_id_from_post > 0) {
        $pt = get_post_type($post_id_from_post);
        if (in_array($pt, $not_restrict_post_types, true)) {
          return true;
        }
      }
      // Neuen Post erstellen (noch keine post_ID vorhanden)
      $post_type_from_post = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : '';
      if (!empty($post_type_from_post) && in_array($post_type_from_post, $not_restrict_post_types, true)) {
        return true;
      }
    }

    // Löschen, Papierkorb, Wiederherstellen: post.php?post=123&action=trash/delete/untrash
    if ($pagenow === 'post.php' && in_array($action, ['trash', 'delete', 'untrash'], true) && $post_id > 0) {
      $pt = get_post_type($post_id);
      if (in_array($pt, $not_restrict_post_types, true)) {
        return true;
      }
    }

    // Bulk-Aktionen auf edit.php (Löschen, Papierkorb, etc. über Checkboxen)
    if ($pagenow === 'edit.php' && $_SERVER['REQUEST_METHOD'] === 'POST') {
      $post_type_from_get = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';
      $post_type_from_post = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : '';
      $bulk_post_type = !empty($post_type_from_get) ? $post_type_from_get : $post_type_from_post;

      if (in_array($bulk_post_type, $not_restrict_post_types, true)) {
        return true;
      }
    }

    return false;
  }

  public function my_local_fields( $fields ) {

    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
      return;
    }

    //g_allergens
    acf_add_local_field_group( array(
      'key' => 'g_allergens',
      'title' => __( 'Allergens', $this->plugin_name ),
      'fields' => array(
        //k_allergens_name
        array(
          'key' => 'k_allergens_name',
          'label' => __('Name', $this->plugin_name),
          'name' => 'allergens_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_allergens_description
        array(
          'key' => 'k_allergens_description',
          'label' => __( 'Description', $this->plugin_name ),
          'name' => 'allergens_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'allergens',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_additives
    acf_add_local_field_group( array(
      'key' => 'g_additives',
      'title' => __( 'Zusatzstoffe', $this->plugin_name ),
      'fields' => array(
        //k_additives_name
        array(
          'key' => 'k_additives_name',
          'label' => __('Name', $this->plugin_name),
          'name' => 'additives_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_additives_description
        array(
          'key' => 'k_additives_description',
          'label' => __( 'Description', $this->plugin_name ),
          'name' => 'additives_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'additives',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_orders
    acf_add_local_field_group( array(
      'key' => 'g_orders',
      'title' => __( 'Orders', $this->plugin_name ),
      'fields' => array(
        //k_order_items
        array(
          'key' => 'k_order_items',
          'label' => __( 'Bestellposition', $this->plugin_name ),
          'name' => 'order_items',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_order_total
        array(
          'key' => 'k_order_total',
          'label' => __( 'Gesamtsumme', $this->plugin_name ),
          'name' => 'order_total',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '€',
        ),
        //k_order_method
        array(
          'key' => 'k_order_method',
          'label' => __( 'Order Type', $this->plugin_name ),
          'name' => 'order_method',
          'aria-label' => '',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => array(
            'delivery' => __( 'Lieferung', $this->plugin_name ),
            'takeaway' => __( 'Abholung', $this->plugin_name ),
          ),
          'default_value' => false,
          'return_format' => 'value',
          'multiple' => 0,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'ui' => 0,
          'ajax' => 0,
          'placeholder' => '',
          'create_options' => 0,
          'save_options' => 0,
        ),
        //k_order_address
        array(
          'key' => 'k_order_address',
          'label' => __( 'Address', $this->plugin_name ),
          'name' => 'order_address',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_order_handy
        array(
          'key' => 'k_order_handy',
          'label' => __( 'Handy', $this->plugin_name ),
          'name' => 'order_handy',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        array(
          'key' => 'k_order_payment_method',
          'label' => __('Zahlungsmittel' , $this->plugin_name),
          'name' => 'order_payment_method',
          'aria-label' => '',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => array(
            'cash' => __('Bar',$this->plugin_name),
            'card' => __('Karte',$this->plugin_name),
          ),
          'default_value' => false,
          'return_format' => 'value',
          'multiple' => 0,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'ui' => 0,
          'ajax' => 0,
          'placeholder' => '',
          'create_options' => 0,
          'save_options' => 0,
        ),
        //k_order_email
        array(
          'key' => 'k_order_email',
          'label' => __('E-Mail',$this->plugin_name),
          'name' => 'order_email',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_order_order_status
        array(
          'key' => 'k_order_order_status',
          'label' => __('Status',$this->plugin_name),
          'name' => 'order_order_status',
          'aria-label' => '',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => array(
            'cancel' => __('Storniert', $this->plugin_name),
            'pending' => __('Bestellung Eingang',$this->plugin_name),
            'confirm' => __('Bestätigt',$this->plugin_name),
            'preparation' => __('Vorbereitung',$this->plugin_name),
            'delivery: in Zustellung' => __('delivery: in Zustellung',$this->plugin_name),
            'delivered' => __('Zugestellt',$this->plugin_name),
            'pick-up' => __('Bereit zur Abholen',$this->plugin_name),
            'picked-up' => __('Abgeholt',$this->plugin_name),
          ),
          'default_value' => false,
          'return_format' => 'value',
          'multiple' => 0,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'ui' => 0,
          'ajax' => 0,
          'placeholder' => '',
          'create_options' => 0,
          'save_options' => 0,
        ),
        //order_description
        array(
          'key' => 'k_order_description',
          'label' => __('Bemerkungen',$this->plugin_name),
          'name' => 'order_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //order_driver_id
        array(
          'key' => 'k_order_driver_id',
          'label' => __('Fahrer', $this->plugin_name),
          'name' => 'order_driver_id',
          'aria-label' => '',
          'type' => 'user',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'role' => array(
            0 => 'contributor',
          ),
          'return_format' => 'array',
          'multiple' => 0,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'bidirectional' => 0,
          'bidirectional_target' => array(
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'orders',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_labeling
    acf_add_local_field_group( array(
      'key' => 'g_labeling',
      'title' => __('Deklarationen',$this->plugin_name),
      'fields' => array(
        //labeling_name
        array(
          'key' => 'k_labeling_name',
          'label' => __('Name',$this->plugin_name),
          'name' => 'labeling_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_labeling_description
        array(
          'key' => 'k_labeling_description',
          'label' => __('Beschreibung',$this->plugin_name),
          'name' => 'labeling_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_labeling_icon
        array(
          'key' => 'k_labeling_icon',
          'label' => __('Bild',$this->plugin_name),
          'name' => 'labeling_icon',
          'aria-label' => '',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'array',
          'library' => 'all',
          'min_width' => 50,
          'min_height' => 50,
          'min_size' => '',
          'max_width' => 50,
          'max_height' => 50,
          'max_size' => '',
          'mime_types' => 'png,jpg,jpeg',
          'allow_in_bindings' => 0,
          'preview_size' => 'full',
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'labeling',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_categories
    acf_add_local_field_group( array(
      'key' => 'g_categories',
      'title' => __('Kategorien',$this->plugin_name),
      'fields' => array(
        //categories_name
        array(
          'key' => 'k_categories_name',
          'label' => __('Name',$this->plugin_name),
          'name' => 'categories_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_categories_description
        array(
          'key' => 'k_categories_description',
          'label' => __('Beschreibung',$this->plugin_name),
          'name' => 'categories_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_categories_image
        array(
          'key' => 'k_categories_image',
          'label' => __('Bild',$this->plugin_name),
          'name' => 'categories_image',
          'aria-label' => '',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'url',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
          'allow_in_bindings' => 0,
          'preview_size' => 'medium',
        ),
        //categories_status
        array(
          'key' => 'k_categories_status',
          'label' => __('Verfügbar',$this->plugin_name),
          'name' => 'categories_status',
          'aria-label' => '',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 1,
          'allow_in_bindings' => 0,
          'ui_on_text' => 'Ja',
          'ui_off_text' => 'Nein',
          'ui' => 1,
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'categories',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_product
    acf_add_local_field_group( array(
      'key' => 'g_product',
      'title' => __('Produkten',$this->plugin_name),
      'fields' => array(
        //k_product_name
        array(
          'key' => 'k_product_name',
          'label' => __('Name',$this->plugin_name),
          'name' => 'product_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_product_image
        array(
          'key' => 'k_product_image',
          'label' => __('Bild',$this->plugin_name),
          'name' => 'product_image',
          'aria-label' => '',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'url',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
          'allow_in_bindings' => 0,
          'preview_size' => 'medium',
        ),
        //k_product_description
        array(
          'key' => 'k_product_description',
          'label' => __('Beschreibung',$this->plugin_name),
          'name' => 'product_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_product_allergens
        array(
          'key' => 'k_product_allergens',
          'label' => __('Allergens',$this->plugin_name),
          'name' => 'product_allergens',
          'aria-label' => '',
          'type' => 'post_object',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'allergens',
          ),
          'post_status' => '',
          'taxonomy' => '',
          'return_format' => 'id',
          'multiple' => 1,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'bidirectional' => 0,
          'ui' => 1,
          'bidirectional_target' => array(
          ),
        ),
        //k_product_additives
        array(
          'key' => 'k_product_additives',
          'label' => __('Zusatzstoffe',$this->plugin_name),
          'name' => 'k_product_additives',
          'aria-label' => '',
          'type' => 'post_object',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'additives',
          ),
          'post_status' => '',
          'taxonomy' => '',
          'return_format' => 'id',
          'multiple' => 1,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'bidirectional' => 0,
          'ui' => 1,
          'bidirectional_target' => array(
          ),
        ),
        //product_category
        array(
          'key' => 'k_product_category',
          'label' => __('Kategorie',$this->plugin_name),
          'name' => 'product_category',
          'aria-label' => '',
          'type' => 'post_object',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'categories',
          ),
          'post_status' => '',
          'taxonomy' => '',
          'return_format' => 'id',
          'multiple' => 1,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'bidirectional' => 0,
          'ui' => 1,
          'bidirectional_target' => array(
          ),
        ),
        //product_status
        array(
          'key' => 'k_product_status',
          'label' => __('Verfügbar',$this->plugin_name),
          'name' => 'product_status',
          'aria-label' => '',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 1,
          'allow_in_bindings' => 0,
          'ui_on_text' => 'Ja',
          'ui_off_text' => 'Nein',
          'ui' => 1,
        ),
        //product_price_type
        array(
          'key' => 'k_product_price_type',
          'label' => __('Preise Typ',$this->plugin_name),
          'name' => 'product_price_type',
          'aria-label' => '',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => array(
            'single' => 'Festpreis',
            'multi' => 'Variante',
          ),
          'default_value' => false,
          'return_format' => 'value',
          'multiple' => 0,
          'allow_null' => 0,
          'allow_in_bindings' => 0,
          'ui' => 0,
          'ajax' => 0,
          'placeholder' => '',
          'create_options' => 0,
          'save_options' => 0,
        ),
        //k_product_price_type_single
        array(
          'key' => 'k_product_price_type_single',
          'label' => __('Preise',$this->plugin_name),
          'name' => 'product_price_type_single',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => array(
            array(
              array(
                'field' => 'k_product_price_type',
                'operator' => '==',
                'value' => 'single',
              ),
            ),
          ),
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => Api_Pd_Wp_Delivery_Env::get( 'D_O__CURRENCY' ),
          'append' => '',
        ),
        //k_product_price_type_multi
        array(
          'key' => 'k_product_price_type_multi',
          'label' => __('Preise',$this->plugin_name),
          'name' => 'product_price_type_multi',
          'aria-label' => '',
          'type' => 'repeater',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => array(
            array(
              array(
                'field' => 'k_product_price_type',
                'operator' => '==',
                'value' => 'multi',
              ),
            ),
          ),
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'layout' => 'table',
          'pagination' => 0,
          'min' => 0,
          'max' => 0,
          'collapsed' => '',
          'button_label' => 'Eintrag hinzufügen',
          'rows_per_page' => 20,
          'sub_fields' => array(
            //k_product_price_type_multi_variant
            array(
              'key' => 'k_product_price_type_multi_variant',
              'label' => __('Variante',$this->plugin_name),
              'name' => 'product_price_type_multi_variant',
              'aria-label' => '',
              'type' => 'post_object',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'post_type' => array(
                0 => 'variant',
              ),
              'post_status' => '',
              'taxonomy' => '',
              'return_format' => 'object',
              'multiple' => 0,
              'allow_null' => 0,
              'allow_in_bindings' => 1,
              'bidirectional' => 0,
              'ui' => 1,
              'bidirectional_target' => array(
              ),
              'parent_repeater' => 'k_product_price_type_multi',
            ),
            //k_product_price_type_multi_variant_price
            array(
              'key' => 'k_product_price_type_multi_variant_price',
              'label' => __('Preise',$this->plugin_name),
              'name' => 'product_price_type_multi_variant_price',
              'aria-label' => '',
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'maxlength' => '',
              'allow_in_bindings' => 0,
              'placeholder' => '',
              'prepend' => Api_Pd_Wp_Delivery_Env::get( 'D_O__CURRENCY' ),
              'append' => '',
              'parent_repeater' => 'k_product_price_type_multi',
            ),
          ),
        ),
        //k_product_additional
        array(
          'key' => 'k_product_additional',
          'label' => __('Extra',$this->plugin_name),
          'name' => 'product_additional',
          'aria-label' => '',
          'type' => 'repeater',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'layout' => 'table',
          'pagination' => 0,
          'min' => 0,
          'max' => 0,
          'collapsed' => '',
          'button_label' => 'Extras hinzufügen',
          'rows_per_page' => 20,
          'sub_fields' => array(
            //k_product_additional_ingredients
            array(
              'key' => 'k_product_additional_ingredients',
              'label' => __('Extra Zutat',$this->plugin_name),
              'name' => 'product_additional_ingredients',
              'aria-label' => '',
              'type' => 'post_object',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'post_type' => array(
                0 => 'ingredients',
              ),
              'post_status' => '',
              'taxonomy' => '',
              'return_format' => 'object',
              'multiple' => 0,
              'allow_null' => 0,
              'allow_in_bindings' => 1,
              'bidirectional' => 0,
              'ui' => 1,
              'bidirectional_target' => array(
              ),
              'parent_repeater' => 'k_product_additional',
            ),
            //k_product_additional_note
            array(
              'key' => 'k_product_additional_note',
              'label' => __('Beschreibung',$this->plugin_name),
              'name' => 'product_additional_note',
              'aria-label' => '',
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'maxlength' => '',
              'allow_in_bindings' => 0,
              'placeholder' => '',
              'prepend' => '',
              'append' => '',
              'parent_repeater' => 'k_product_additional',
            ),
            //k_product_additional_price
            array(
              'key' => 'k_product_additional_price',
              'label' => __('Preise',$this->plugin_name),
              'name' => 'product_additional_price',
              'aria-label' => '',
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'maxlength' => '',
              'allow_in_bindings' => 0,
              'placeholder' => '',
              'prepend' => Api_Pd_Wp_Delivery_Env::get( 'D_O__CURRENCY' ),
              'append' => '',
              'parent_repeater' => 'k_product_additional',
            ),
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'product',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_variant
    acf_add_local_field_group( array(
      'key' => 'g_variant',
      'title' => __('Varianten',$this->plugin_name),
      'fields' => array(
        array(
          'key' => 'k_variant_name',
          'label' => __('Name',$this->plugin_name),
          'name' => 'variant_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => Api_Pd_Wp_Delivery_Env::get( 'D_O__CURRENCY' ),
          'append' => '',
        ),
        //k_variant_description
        array(
          'key' => 'k_variant_description',
          'label' => __('Beschreibung',$this->plugin_name),
          'name' => 'variant_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_variant_status
        array(
          'key' => 'k_variant_status',
          'label' => __('Verfügbar',$this->plugin_name),
          'name' => 'variant_status',
          'aria-label' => '',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => true,
          'allow_in_bindings' => 0,
          'ui_on_text' => 'Ja',
          'ui_off_text' => 'Nein',
          'ui' => 1,
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'variant',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

    //g_ingredients
    acf_add_local_field_group( array(
      'key' => 'g_ingredients',
      'title' => __('Zutaten',$this->plugin_name),
      'fields' => array(
        //k_ingredients_name
        array(
          'key' => 'k_ingredients_name',
          'label' => __('Name',$this->plugin_name),
          'name' => 'ingredients_name',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        //k_ingredients_description
        array(
          'key' => 'k_ingredients_description',
          'label' => __('Beschreibung',$this->plugin_name),
          'name' => 'ingredients_description',
          'aria-label' => '',
          'type' => 'textarea',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'rows' => '',
          'placeholder' => '',
          'new_lines' => '',
        ),
        //k_ingredients_status
        array(
          'key' => 'k_ingredients_status',
          'label' => __('Verfügbar',$this->plugin_name),
          'name' => 'ingredients_status',
          'aria-label' => '',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 1,
          'allow_in_bindings' => 0,
          'ui_on_text' => 'Ja',
          'ui_off_text' => 'Nein',
          'ui' => 1,
        ),
        //k_ingredients_allergens
        array(
          'key' => 'k_ingredients_allergens',
          'label' => __('Allergens',$this->plugin_name),
          'name' => 'ingredients_allergens',
          'aria-label' => '',
          'type' => 'post_object',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'post_type' => array(
            0 => 'allergens',
          ),
          'post_status' => '',
          'taxonomy' => '',
          'return_format' => 'id',
          'multiple' => 1,
          'allow_null' => 0,
          'allow_in_bindings' => 1,
          'bidirectional' => 0,
          'ui' => 1,
          'bidirectional_target' => array(
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'ingredients',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 0,
      'display_title' => '',
    ) );

  }



}
