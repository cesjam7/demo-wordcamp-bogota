<?php add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'child-style',
		get_stylesheet_uri(),
		array( 'parenthandle' ),
		wp_get_theme()->get( 'Version' ) // This only works if you have Version defined in the style header.
	);
}

// Agregar campos al producto
add_action('woocommerce_before_add_to_cart_button','o612_add_custom_fields');
function o612_add_custom_fields() {
  if (empty($_GET['costo'])) $_GET['costo'] = 100;
  $template_uri = get_bloginfo('template_url'); ?>
  <style>
    .price {
      display: none;
    }
  </style>
  <br><br>
  <br><br>
  <label>Participante:</label>
  <input type="text" name="participante" required placeholder="Ingrese nombre">
  <br><br>
  <label>Costo:</label>
  <h4 style="margin: 0;"><?php echo wc_price($_GET['costo']); ?></h4>
  <input type="hidden" name="costo" value="<?php echo $_GET['costo']; ?>">
  <br><br>
  <?php $content = ob_get_contents();
  ob_end_flush();
  return $content;
}

// Agregar datos al item del carrito
add_filter('woocommerce_add_cart_item_data','o612_add_item_data',10,3);
function o612_add_item_data($cart_item_data, $product_id, $variation_id) {
  if(isset($_REQUEST['participante'])) {
      $cart_item_data['participante'] = sanitize_text_field($_REQUEST['participante']);
  }
  if(isset($_REQUEST['costo'])) {
      $cart_item_data['costo'] = sanitize_text_field($_REQUEST['costo']);
  }
  return $cart_item_data;
}

// Mostrar datos extras en el item del carrito
add_filter('woocommerce_get_item_data','o612_add_item_meta',10,2);
function o612_add_item_meta($item_data, $cart_item) {

    if(array_key_exists('participante', $cart_item)) {
        $item_data[] = array(
            'key'   => 'Participante',
            'value' => $cart_item['participante']
        );
    }
    if(array_key_exists('costo', $cart_item)) {
        $item_data[] = array(
            'key'   => 'Costo',
            'value' => $cart_item['costo']
        );
    }
    return $item_data;
}

// Agregar datos extras al item de la orden
add_action( 'woocommerce_checkout_create_order_line_item', 'o612_add_custom_order_line_item_meta',10,4 );
function o612_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order) {

    if(array_key_exists('participante', $values)) {
        $item->add_meta_data('Participante',$values['participante']);
    }
}

// Modificar precio de los items del carrito
add_action( 'woocommerce_before_calculate_totals', 'tours_woocommerce_before_calculate_totals' );
function tours_woocommerce_before_calculate_totals( $cart_object ) {
  foreach ( $cart_object->cart_contents as $key => $value ) {
    $value['data']->set_price($value['costo']);
  }
}