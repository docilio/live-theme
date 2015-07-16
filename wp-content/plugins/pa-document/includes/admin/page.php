<?php

/*-----------------------------------------------------------------------------------*/
/* Register the admin page with the 'admin_menu' */
/*-----------------------------------------------------------------------------------*/

function pado_admin_menu() {
	$page = add_submenu_page( 'options-general.php', __( 'PA Document', 'pressapps' ), __( 'PA Document', 'pressapps' ), 'manage_options', 'document-options', 'pado_options', 99 );
}
add_action( 'admin_menu', 'pado_admin_menu' );


/*-----------------------------------------------------------------------------------*/
/* Load HTML that will create the outter shell of the admin page */
/*-----------------------------------------------------------------------------------*/

function pado_options() {

	// Check that the user is able to view this page.
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'pressapps' ) ); ?>

	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'Document Settings', 'pressapps' ); ?></h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'pado_setup_options' ); ?>
			<?php do_settings_sections( 'pado_setup_options' ); ?>
			<?php submit_button(); ?>
		</form>

	</div>
<?php }

/*-----------------------------------------------------------------------------------*/
/* Registers all sections and fields with the Settings API */
/*-----------------------------------------------------------------------------------*/

function pado_init_settings_registration() {
	$option_name = 'pado_options';

	// Check if settings options exist in the database. If not, add them.
	if ( get_option( $option_name ) )
		add_option( $option_name );

	// Define settings sections.
	add_settings_section( 'pado_setup_section', __( 'Setup', 'pressapps' ), 'pado_setup_options', 'pado_setup_options' );

	add_settings_field( 'reorder', __( 'Reorder', 'pressapps' ), 'pado_settings_field_select', 'pado_setup_options', 'pado_setup_section', array(
		'options-name' => $option_name,
		'id'				=> 'reorder',
		'class' 			=> '',
		'value'			=> array(
								'0' => __( 'Disabled', 'pressapps' ),
								'1' => __( 'Enabled', 'pressapps' ),
								),
		'label'			=> __( 'Enable document posts and categories drag and drop reorder feature.', 'pressapps' ),
	) );
	add_settings_field( 'voting', __( 'Voting', 'pressapps' ), 'pado_settings_field_select', 'pado_setup_options', 'pado_setup_section', array(
		'options-name' => $option_name,
		'id'				=> 'voting',
		'class' 			=> '',
		'value'			=> array(
								'0' => __( 'Disabled', 'pressapps' ),
								'1' => __( 'Public Voting', 'pressapps' ),
								'2' => __( 'Logged In Users Only', 'pressapps' ),
								),
		'label'			=> __( 'Document post voting.', 'pressapps' ),
	) );
	add_settings_field( 'icon', __( 'Icon', 'pressapps' ), 'pado_settings_field_select', 'pado_setup_options', 'pado_setup_section', array(
		'options-name' => $option_name,
		'id'				=> 'icon',
		'class' 			=> '',
		'value'			=> array(
								'none' => __( 'None' , 'pressapps' ),
								'thumbs' => __( 'Thumb', 'pressapps' ),
								),
		'label'			=> __( 'Select vote icon.', 'pressapps' ),
	) );
	add_settings_field( 'color', __( 'Color', 'pressapps' ), 'pado_settings_field_color', 'pado_setup_options', 'pado_setup_section', array(
		'options-name' => $option_name,
		'id'				=> 'color',
		'class'			=> '',
		'value'			=> '',
		'label'			=> __( 'Set links and category headline color.', 'pressapps' ),
	) );

	add_settings_field( 'custom_css', __( 'Custom CSS', 'pressapps' ), 'pado_settings_field_textarea', 'pado_setup_options', 'pado_setup_section', array(
		'options-name' => $option_name,
		'id'				=> 'custom-css',
		'class'			=> '',
		'value'			=> '',
		'label'			=> __( 'Add custom CSS code.', 'pressapps' ),
	) );


	// Register settings with WordPress so we can save to the Database
	register_setting( 'pado_setup_options', $option_name, 'pado_options_sanitize' );
}
add_action( 'admin_init', 'pado_init_settings_registration' );

/*-----------------------------------------------------------------------------------*/
/* add_settings_section() function for the widget options */
/*-----------------------------------------------------------------------------------*/

function pado_setup_options() {
	//echo '<p>' . __( 'You can add video posts to your site using [video] shortcode.', 'pressapps' ) . '.</p>';
}

/*-----------------------------------------------------------------------------------*/
/* he callback function to display textareas */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_textarea( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<label for="<?php echo $args['id']; ?>"><?php esc_attr_e( $args['label'] ); ?></label><br />
	<textarea name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" class="<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" cols="60" rows="7"><?php esc_attr_e( $options[ $args['id'] ] ); ?></textarea>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display checkboxes */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_checkbox( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" <?php if ( ! empty( $args['class'] ) ) echo 'class="' . $args['class'] . '" '; ?>value="<?php esc_attr_e( $args['value'] ); ?>" <?php if ( isset( $options[ $args['id'] ] ) ) checked( $args['value'], $options[ $args['id'] ], true ); ?> />
	<label for="<?php echo $args['id']; ?>"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display selection dropdown */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_select( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<select name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" <?php if ( ! empty( $args['class'] ) ) echo 'class="' . $args['class'] . '" '; ?>>
		<?php foreach ( $args['value'] as $key => $value ) : ?>
			<option value="<?php esc_attr_e( $key ); ?>"<?php if ( isset( $options[ $args['id'] ] ) ) selected( $key, $options[ $args['id'] ], true ); ?>><?php esc_attr_e( $value ); ?></option>
		<?php endforeach; ?>
	</select>
	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display text field */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_text( $args ) {

	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" type="text" class="regular-text code<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" value="<?php if ( isset ( $options[ $args['id'] ] )) { esc_attr_e( $options[ $args['id'] ] ) ;} else { echo ''; } ?>"></input>

	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* Color picker */
/*-----------------------------------------------------------------------------------*/

function pado_enqueue_color_picker( ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-script', PADO_PLUGIN_ASSETS_URL . 'js/admin.js', array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'pado_enqueue_color_picker' );

/*-----------------------------------------------------------------------------------*/
/* The callback function to display color picker */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_color( $args ) {

	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" class="wp-color-picker-field<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" value="<?php if ( isset ( $options[ $args['id'] ] )) { esc_attr_e( $options[ $args['id'] ] ) ;} else { echo ''; } ?>"></input>

	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display info */
/*-----------------------------------------------------------------------------------*/

function pado_settings_field_info( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<p><?php esc_attr_e( $args['value'] ); ?></p>

<?php }


/*-----------------------------------------------------------------------------------*/
/* Sanitization function */
/*-----------------------------------------------------------------------------------*/

function pado_options_sanitize( $input ) {

	// Set array for the sanitized options
	$output = array();

	// Loop through each of $input options and sanitize them.
	foreach ( $input as $key => $value ) {
		if ( isset( $input[ $key ] ) )
			$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
	}

	return apply_filters( 'pado_options_sanitize', $output, $input );
}

