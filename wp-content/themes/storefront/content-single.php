<?php
/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	do_action( 'storefront_single_post_top' );

	/**
	 * Functions hooked into storefront_single_post add_action
	 *
	 * @hooked storefront_post_header          - 10
	 * @hooked storefront_post_content         - 30
	 */
	do_action( 'storefront_single_post' );

	// register_acpt_post_type([ 
	// 	'post_name' => 'book', 
	// 	'singular_label' => 'New book', 
	// 	'plural_label' => 'New books', 
	// 	'icon' => 'admin-appearance',
	// 	'supports' => [ 
	// 		'title',
	// 		'editor', 
	// 		'comments', 
	// 		'revisions', 
	// 		'trackbacks', 
	// 		'author', 
	// 		'excerpt',
	// 	], 
	// 	'labels' => [], 
	// 	'settings' => [], 
	// ]);
	// echo 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaadfwfda';

	//delete_acpt_post_type
	// delete_acpt_post_type('book', true);

	//assoc_acpt_taxonomy_to_acpt_post
	// assoc_acpt_taxonomy_to_acpt_post('ctax', 'post');

	//remove_assoc_acpt_taxonomy_from_acpt_post
	// remove_assoc_acpt_taxonomy_from_acpt_post('ctax', 'post');

	//save_acpt_form
	// save_acpt_form([
	// 	'name' => 'add-cpt',
	// 	'label' => 'Add CPT',
	// 	'key' => '96b3303d',
	// 	'action' => 'Post management',
	// 	'fields' => [],
	// 	'meta' => []
	// ]);

	//save_acpt_form_field
	// echo save_acpt_form_field([
	// 	'form_name' => 'add-cpt',
	// 	'key' => '',
	// 	'group' => 'group-first',
	// 	'name' => 'cpt-fields-time',
	// 	'label' => 'cpt-fields-time',
	// 	'type' => 'Time',
	// 	'description' => 'description',
	// 	'isRequired' => false,
	// 	'extra' => [],
	// 	'settings' => [],
	//  ]);

	//get_acpt_meta_group_objects
	echo '<pre>';
	print_r (get_acpt_meta_group_objects());
	echo '</pre>';

	//get_acpt_meta_group_object
	echo '<pre>';
	print_r (get_acpt_meta_group_object('group-first'));
	echo '</pre>';

	/**
	 * Functions hooked in to storefront_single_post_bottom action
	 *
	 * @hooked storefront_post_nav         - 10
	 * @hooked storefront_display_comments - 20
	 */
	do_action( 'storefront_single_post_bottom' );
	?>

</article><!-- #post-## -->
