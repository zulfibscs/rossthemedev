<?php
/**
 * Footer CTA Template
 */
$footer_options = get_option('ross_theme_footer_options');

if ( function_exists('ross_theme_should_show_footer_cta') && ! ross_theme_should_show_footer_cta() ) {
	return;
}

$cta_title = $footer_options['cta_title'] ?? '';
$cta_text = $footer_options['cta_text'] ?? '';
$cta_button_text = $footer_options['cta_button_text'] ?? '';
$cta_button_url = $footer_options['cta_button_url'] ?? '#';
?>

<div class="footer-cta">
	<div class="<?php echo esc_attr( (isset($footer_options['footer_width']) && $footer_options['footer_width'] === 'full') ? 'container-fluid' : 'container' ); ?>">
		<div class="footer-cta-inner">
			<?php if ( ! empty( $cta_title ) ) : ?>
				<h2 class="footer-cta-title"><?php echo esc_html( $cta_title ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $cta_text ) ) : ?>
				<div class="footer-cta-text"><?php echo wp_kses_post( $cta_text ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $cta_button_text ) ) : ?>
				<div class="footer-cta-action">
					<a class="btn btn-primary" href="<?php echo esc_url( $cta_button_url ); ?>">
						<?php echo esc_html( $cta_button_text ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
		<?php if (function_exists('ross_theme_should_show_copyright') && ross_theme_should_show_copyright()):
			$copyright_bg = isset($footer_options['copyright_bg_color']) ? sanitize_hex_color($footer_options['copyright_bg_color']) : '';
			$copyright_text_color = isset($footer_options['copyright_text_color']) ? sanitize_hex_color($footer_options['copyright_text_color']) : '';
			$copyright_alignment = isset($footer_options['copyright_alignment']) ? esc_attr($footer_options['copyright_alignment']) : 'center';

			$copyright_styles = array();
			if ($copyright_bg) $copyright_styles[] = 'background:' . esc_attr($copyright_bg);
			if ($copyright_text_color) $copyright_styles[] = 'color:' . esc_attr($copyright_text_color);
			$copyright_style_attr = !empty($copyright_styles) ? 'style="' . esc_attr(implode(';', $copyright_styles)) . '"' : '';
		?>
		<div class="footer-copyright" <?php echo $copyright_style_attr; ?>>
			<div class="<?php echo esc_attr( (isset($footer_options['footer_width']) && $footer_options['footer_width'] === 'full') ? 'container-fluid' : 'container' ); ?>">
				<div class="copyright-inner" style="text-align:<?php echo esc_attr($copyright_alignment); ?>;">
					<div class="copyright-text">
						<?php echo wp_kses_post( ross_theme_get_copyright_text() ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

