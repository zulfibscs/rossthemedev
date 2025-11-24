<?php
/**
 * Footer Widgets Template
 */
$footer_options = get_option('ross_theme_footer_options');

if ( function_exists('ross_theme_should_show_footer_widgets') && ! ross_theme_should_show_footer_widgets() ) {
	return;
}

$default_cols = isset( $footer_options['footer_columns'] ) ? intval( $footer_options['footer_columns'] ) : 4;
$columns = max( 1, min( 4, $default_cols ) );
$container_class = ( isset( $footer_options['footer_width'] ) && $footer_options['footer_width'] === 'full' ) ? 'container-fluid' : 'container';
?>

<div class="footer-widgets">
	<div class="<?php echo esc_attr( $container_class ); ?>">
		<div class="footer-columns footer-columns--<?php echo esc_attr( $columns ); ?>">
			<?php
			for ( $i = 1; $i <= $columns; $i++ ) :
				$id = 'footer-' . $i;
				?>
				<div class="footer-column footer-column-<?php echo esc_attr( $i ); ?>">
					<?php
					if ( is_active_sidebar( $id ) ) {
						dynamic_sidebar( $id );
					} else {
						echo '<div class="footer-placeholder">';
						echo '<!-- ' . esc_html( $id ) . ' is empty -->';
						echo '</div>';
					}
					?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</div>

