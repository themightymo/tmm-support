<?php
/*
Plugin Name: The Mighty Mo! Clients Support Plugin
Plugin URI: http://www.themightymo.com/
Description: Adds support ticket functionality
Author: themightymo
Version: 1.0
Author URI: http://www.themightymo.com/
Text Domain: tmm-support
License: GPLv2
*/

// Create "tmm_support_ticket" post type
if ( ! function_exists('create_tmm_support_ticket_cpt') ) {

// Register Custom Post Type
function create_tmm_support_ticket_cpt() {

	$labels = array(
		'name'                  => _x( 'Support Tickets', 'Post Type General Name', 'tmm_support' ),
		'singular_name'         => _x( 'Support Ticket', 'Post Type Singular Name', 'tmm_support' ),
		'menu_name'             => __( 'Support Tickets', 'tmm_support' ),
		'name_admin_bar'        => __( 'Support Ticket', 'tmm_support' ),
		'archives'              => __( 'Support Ticket Archives', 'tmm_support' ),
		'parent_item_colon'     => __( 'Parent Support Ticket:', 'tmm_support' ),
		'all_items'             => __( 'All Support Tickets', 'tmm_support' ),
		'add_new_item'          => __( 'Add New Support Ticket', 'tmm_support' ),
		'add_new'               => __( 'Add New', 'tmm_support' ),
		'new_item'              => __( 'New Support Ticket', 'tmm_support' ),
		'edit_item'             => __( 'Edit Support Ticket', 'tmm_support' ),
		'update_item'           => __( 'Update Support Ticket', 'tmm_support' ),
		'view_item'             => __( 'View Support Ticket', 'tmm_support' ),
		'search_items'          => __( 'Search Support Tickets', 'tmm_support' ),
		'not_found'             => __( 'Not found', 'tmm_support' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'tmm_support' ),
		'featured_image'        => __( 'Featured Image', 'tmm_support' ),
		'set_featured_image'    => __( 'Set featured image', 'tmm_support' ),
		'remove_featured_image' => __( 'Remove featured image', 'tmm_support' ),
		'use_featured_image'    => __( 'Use as featured image', 'tmm_support' ),
		'insert_into_item'      => __( 'Insert into Support Ticket', 'tmm_support' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Support Ticket', 'tmm_support' ),
		'items_list'            => __( 'Support Ticket list', 'tmm_support' ),
		'items_list_navigation' => __( 'Support Ticket list navigation', 'tmm_support' ),
		'filter_items_list'     => __( 'Filter Support Ticket list', 'tmm_support' ),
	);
	$args = array(
		'label'                 => __( 'Support Ticket', 'tmm_support' ),
		'description'           => __( 'Support Tickets', 'tmm_support' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields', ),
		'taxonomies'            => array( 'ticket_status_taxonomy' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-heart',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => false,
		'has_archive'           => true,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'tmm_support_ticket', $args );

}
add_action( 'init', 'create_tmm_support_ticket_cpt', 0 );

}

// Create Ticket Status taxonomy
if ( ! function_exists( 'create_ticket_status_taxonomy' ) ) {

// Register Custom Taxonomy
function create_ticket_status_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Ticket Statuses', 'Taxonomy General Name', 'tmm_support' ),
		'singular_name'              => _x( 'Ticket Status', 'Taxonomy Singular Name', 'tmm_support' ),
		'menu_name'                  => __( 'Ticket Status', 'tmm_support' ),
		'all_items'                  => __( 'All Ticket Statuses', 'tmm_support' ),
		'parent_item'                => __( 'Parent Ticket Status', 'tmm_support' ),
		'parent_item_colon'          => __( 'Parent Ticket Status:', 'tmm_support' ),
		'new_item_name'              => __( 'New Ticket Status Name', 'tmm_support' ),
		'add_new_item'               => __( 'Add New Ticket Status', 'tmm_support' ),
		'edit_item'                  => __( 'Edit Ticket Status', 'tmm_support' ),
		'update_item'                => __( 'Update Ticket Status', 'tmm_support' ),
		'view_item'                  => __( 'View Ticket Status', 'tmm_support' ),
		'separate_items_with_commas' => __( 'Separate Ticket Statuses with commas', 'tmm_support' ),
		'add_or_remove_items'        => __( 'Add or remove Ticket Statuses', 'tmm_support' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'tmm_support' ),
		'popular_items'              => __( 'Popular Ticket Statuses', 'tmm_support' ),
		'search_items'               => __( 'Search Ticket Statuses', 'tmm_support' ),
		'not_found'                  => __( 'Not Found', 'tmm_support' ),
		'no_terms'                   => __( 'No Ticket Statuses', 'tmm_support' ),
		'items_list'                 => __( 'Ticket Statuses list', 'tmm_support' ),
		'items_list_navigation'      => __( 'Ticket Statuses list navigation', 'tmm_support' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'ticket_status_taxonomy', array( 'tmm_support_ticket' ), $args );

}
add_action( 'init', 'create_ticket_status_taxonomy', 0 );

}


// Create function that displays all of the currently-logged-in user's tmm_support_ticket posts on the "My Account" page
function display_customer_support_tickets() {
	global $current_user;
    get_currentuserinfo();
    
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	// WP_Query arguments
	$args = array (
		'post_type'              => array( 'tmm_support_ticket' ),
		'author'                 => $current_user->ID,
		//'nopaging'               => false,
		'paged'					 => $paged,
		'posts_per_page'         => '10',
		'posts_per_archive_page' => '10',
		'order'                  => 'ASC',
		'orderby'                => 'modified',
	);
	
	// The Query
	$query = new WP_Query( $args );
	echo '<pre>';
	//var_dump($query);
	echo '</pre>';
	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<pre>';
			//var_dump($query);
			echo '</pre>';
		}
	} 
	
	// Restore original Post Data
	wp_reset_postdata();?>
	<div class="my_support_tickets">
		<h2>My Support Tickets</h2>
		<table class="shop_table shop_table_responsive my_account_subscriptions my_account_orders">
			<thead>
				<tr>
					<th class="subscription-id order-number"><span class="nobr">Support Ticket Title</span></th>
					<th class="subscription-status order-status"><span class="nobr">Last Response Date</span></th>
					<th class="subscription-next-payment order-date"><span class="nobr">Status</span></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						
						// Reset comment variables
						$comment_author = null;
						$comment_date = null;
						$comment_content = null;
						$comments = get_comments( array( 'number' => 1, 'post_id' => get_the_ID() ) ); 
						/*echo '<pre>';
						print_r($comments);
						echo '</pre>';*/
						foreach($comments as $comment) :
							$comment_author = $comment->comment_author;
							$comment_date = $comment->comment_date;
							$comment_content = $comment->comment_content;
							$comment_ID = $comment->comment_ID;
							$comment_post_ID = $comment->comment_post_ID;
						endforeach;
						?>

						<tr class="order">
							<td class="subscription-id order-number">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</td>
							<td class="subscription-status order-status" style="text-align:left; white-space:nowrap;">
								<a href="<?php echo get_permalink($comment_post_ID); ?>#comment-<?php echo $comment_ID; ?>" title="By <?php echo $comment_author; ?>: <?php echo $comment_content; ?>"><?php echo $comment_date; ?></a>	
								<a href="" rel="external nofollow" title="<?php echo $title; ?>"> <?php echo $title; ?></a>
		
							</td>
							<td class="subscription-next-payment order-date" data-title="Next Payment">
								STATUS (Active or Closed)
							</td>
						</tr>
						<?php 
					}
					if ($query->max_num_pages > 1) { // check if the max number of pages is greater than 1  ?>
						<nav class="prev-next-posts">
							<div class="prev-posts-link">
								<?php echo get_next_posts_link( 'Older Tickets', $query->max_num_pages ); // display older posts link ?>
							</div>
							<div class="next-posts-link">
								<?php echo get_previous_posts_link( ' Newer Tickets' ); // display newer posts link ?>
							</div>
						</nav><?php 
					}
				} else {
					// no posts found
				}
					
				// Restore original Post Data
				wp_reset_postdata();
				?>
			</tbody>
		
		</table>
	</div><!-- .my_support_tickets --><?php
	
	echo '<div style="margin-bottom:1em;padding:1em;border:3px solid #000;border-radius:10px;">' . do_shortcode ( '[gravityform id="1" title="true" description="true" ajax="true"]' ) . '</div>';
}
add_action ('woocommerce_before_my_account', 'display_customer_support_tickets');




// Create front-end ticket creation form using ACF