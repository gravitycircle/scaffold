<?php
/**
 * Sample Block Template Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

if($is_preview) {
	$sample = new sampleBlock($post_id, $block['id'], $block);
}
else{
	$sample = new sampleBlock($post_id, $block['id']);
}

echo $sample->render_admin_block();