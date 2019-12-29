<?php

namespace Toucan;

/**
 * Class ColorPalette
 */
class ColorPalette {

	const OPTION_NAME = 'toucan_color_palette';

	/**
	 * Initialize the plugin and hook up to WordPress and CMB2.
	 */
	static public function bootstrap() {
		$plugin = new self();

		add_action( 'init', [ $plugin, 'init' ] );
		add_action( 'cmb2_admin_init', [ $plugin, 'registerPaletteMetaBoxes' ] );
	}

	/**
	 * Helper method for standardizing option values.
	 *
	 * @return array
	 */
	static public function values() {
		$options = get_option( self::OPTION_NAME, [] );

		return array_replace( [
			'disable_custom_colors' => !empty( $options['disable_custom_colors'] ),
			'colors' => [],
		], $options );
	}

	/**
	 * Update the color palette using theme supports.
	 */
	public function init() {
		$values = self::values();

		if ( !empty( $values['colors'] ) ) {
			add_theme_support( 'editor-color-palette', $values['colors'] );
		}


		if ( $values['disable_custom_colors'] ) {
			add_theme_support( 'disable-custom-colors' );
		}
	}

	/**
	 * Registers options page menu item and form.
	 */
	public function registerPaletteMetaBoxes() {

		$box = new_cmb2_box( [
			'id'           => 'toucan_color_palette_config',
			'title'        => esc_html__( 'Toucan - Gutenberg Color Palette' ),
			'menu_title'   => esc_html__( 'Toucan Colors' ),
			'object_types' => [ 'options-page' ],
			'parent_slug'  => 'options-general.php',
			'option_key'   => self::OPTION_NAME,
			'capability'   => 'manage_options',
		] );

		$box->add_field( [
			'id'          => 'disable_custom_colors',
			'type'        => 'checkbox',
			'name'        => __( 'Disable Non-Palette Colors' ),
			'description' => __( 'Prevent content editors from being able to select colors that are not defined in this palette.' ),
		] );

		$group_id = $box->add_field( [
			'id'          => 'colors',
			'name'        => __( 'Colors' ),
			'type'        => 'group',
			'description' => __( 'Custom color palette made available when using the Gutenberg editor.' ),
			'options'     => [
				'group_title'    => __( 'Color {#}' ),
				'add_button'     => __( 'Add Another Color' ),
				'remove_button'  => __( 'Remove Color' ),
				'sortable'       => true,
				'remove_confirm' => esc_html__( 'Are you sure you want to remove this color?' ),
			],
		] );
		$box->add_group_field( $group_id, [
			'name'       => __( 'Color Name' ),
			'desc'       => __( 'Human readable name for the color.' ),
			'id'         => 'name',
			'type'       => 'text',
			'attributes' => [
				'required' => 'required',
			],
		] );
		$box->add_group_field( $group_id, [
			'name'            => __( 'Color Slug' ),
			'desc'            => __( 'Machine safe name for the color. Letters, numbers, and dashes only.' ),
			'id'              => 'slug',
			'type'            => 'text',
			'sanitization_cb' => 'sanitize_title',
			'attributes'      => [
				'required' => 'required',
			],
		] );
		$box->add_group_field( $group_id, [
			'name'    => __( 'Color Code' ),
			'id'      => 'color',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		] );
	}

}
