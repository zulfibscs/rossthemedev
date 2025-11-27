<?php
/**
 * Minimal Footer Template
 */
$footer_options = get_option('ross_theme_footer_options');
?>

<footer class="site-footer site-footer--minimal">
	<div class="<?php echo esc_attr( (isset($footer_options['footer_width']) && $footer_options['footer_width'] === 'full') ? 'container-fluid' : 'container' ); ?>">
		<div class="footer-minimal-inner">
			<div class="copyright-text">
				<?php echo wp_kses_post( ross_theme_get_copyright_text() ); ?>
			</div>

			<?php if ( function_exists('ross_theme_should_show_social_icons') && ross_theme_should_show_social_icons() ) : ?>
				<div class="footer-social">
					<?php
					$facebook = $footer_options['facebook_url'] ?? '';
					$linkedin = $footer_options['linkedin_url'] ?? '';
					if ( ! empty( $facebook ) ) {
						echo '<a class="social-icon social-icon--facebook" href="' . esc_url( $facebook ) . '" target="_blank" rel="noopener noreferrer">Facebook</a>';
					}
					if ( ! empty( $linkedin ) ) {
						echo '<a class="social-icon social-icon--linkedin" href="' . esc_url( $linkedin ) . '" target="_blank" rel="noopener noreferrer">LinkedIn</a>';
					}
					?>
				</div>
			<?php endif; ?>
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
			<div class="container">
				<div class="copyright-inner" style="text-align:<?php echo esc_attr($copyright_alignment); ?>;">
					<div class="copyright-text">
						<?php echo wp_kses_post( ross_theme_get_copyright_text() ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
		<?php if (!empty($footer_options['custom_footer_js'])): ?>
			<script><?php echo esc_html($footer_options['custom_footer_js']); ?></script>
		<?php endif; ?>
	</footer>

