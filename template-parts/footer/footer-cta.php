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

<?php
// CTA wrapper with modifiers so we can style via dynamic CSS
$cta_alignment = isset($footer_options['cta_alignment']) ? $footer_options['cta_alignment'] : 'center';
$cta_animation = isset($footer_options['cta_animation']) ? $footer_options['cta_animation'] : 'none';
$cta_gap = isset($footer_options['cta_gap']) ? intval($footer_options['cta_gap']) : 12;
$cta_icon = isset($footer_options['cta_icon']) ? $footer_options['cta_icon'] : '';
$cta_icon_color = isset($footer_options['cta_icon_color']) ? $footer_options['cta_icon_color'] : '';
$cta_layout_direction = isset($footer_options['cta_layout_direction']) ? $footer_options['cta_layout_direction'] : 'row';
$cta_layout_wrap = isset($footer_options['cta_layout_wrap']) && $footer_options['cta_layout_wrap'] ? true : false;
?>
<?php
$dir_class = 'footer-cta--dir-' . esc_attr($cta_layout_direction);
$wrap_class = $cta_layout_wrap ? ' footer-cta--wrap' : '';
?>
<div class="footer-cta <?php echo esc_attr($dir_class); ?><?php echo esc_attr($wrap_class); ?> footer-cta--align-<?php echo esc_attr($cta_alignment); ?> footer-cta--anim-<?php echo esc_attr($cta_animation); ?>" data-cta-gap="<?php echo esc_attr($cta_gap); ?>">
	<div class="<?php echo esc_attr( (isset($footer_options['footer_width']) && $footer_options['footer_width'] === 'full') ? 'container-fluid' : 'container' ); ?>">
		<div class="footer-cta-inner">
			<?php if ( ! empty( $cta_icon ) ) : ?>
				<span class="cta-icon <?php echo esc_attr($cta_icon); ?>" style="<?php echo !empty($cta_icon_color) ? 'color:' . esc_attr($cta_icon_color) . ';' : ''; ?>"></span>
			<?php endif; ?>
			<?php if ( ! empty( $cta_title ) ) : ?>
				<h2 class="footer-cta-title"><?php echo esc_html( $cta_title ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $cta_text ) ) : ?>
				<div class="footer-cta-text"><?php echo wp_kses_post( $cta_text ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $cta_button_text ) ) : ?>
				<div class="footer-cta-action">
					<a class="btn btn-primary" href="<?php echo esc_url( $cta_button_url ); ?>" role="button">
						<?php echo esc_html( $cta_button_text ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php if ($cta_animation !== 'none') : ?>
	<script>(function(){document.addEventListener('DOMContentLoaded',function(){try{var el=document.querySelector('.footer-cta.footer-cta--anim-<?php echo esc_js($cta_animation); ?>'); if(el){ setTimeout(function(){ el.classList.add('visible'); }, 60);} }catch(e){} });})();</script>
<?php endif; ?>
<?php // Custom CTA output (HTML/CSS/JS) — only when enabled ?>
<?php if (!empty($footer_options['enable_custom_cta'])): ?>
	<?php if (!empty($footer_options['custom_cta_html'])): ?>
		<div class="footer-cta-custom-html"><?php echo wp_kses_post($footer_options['custom_cta_html']); ?></div>
	<?php endif; ?>
	<?php if (!empty($footer_options['custom_cta_js'])): ?>
		<script><?php echo esc_html($footer_options['custom_cta_js']); ?></script>
	<?php endif; ?>
<?php endif; ?>
		<!-- Copyright is handled by footer templates; CTA template should only render the CTA area -->

