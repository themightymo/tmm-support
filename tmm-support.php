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

// Load stylesheet
function add_tmm_support_scripts() {
	wp_register_style( 'tmm-support-css',  plugin_dir_url( __FILE__ ) . 'tmm-support.css' );
	wp_enqueue_style( 'tmm-support-css' );
}
add_action( 'wp_enqueue_scripts', 'add_tmm_support_scripts' );



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
			//'show_tagcloud'              => true,
			//'rewrite'					=> array( 'slug' => 'ticket-status' ),
			'query_var' => true
		);
		
		register_taxonomy( 'ticket_status', array( 'tmm_support_ticket' ), $args );
		
		/*
			Now that the "ticket_status" taxonomy has been created, let's define the taxonomy's terms.
			via https://wordpress.stackexchange.com/a/30819
		*/
		// If the "Active" and "Closed" terms don't exist yet, create them.
		$parent_term = term_exists( 'active', 'closed' ); // array is returned if taxonomy is given
		$parent_term_id = $parent_term['term_id'];         // get numeric term id
		wp_insert_term(
			'Active', // the term 
			'ticket_status', // the taxonomy
			array(
				'description'=> 'Active Ticket',
				'slug' => 'active',
				'parent'=> $parent_term['term_id']  // get numeric term id
			)
		);
		wp_insert_term(
			'Closed', // the term 
			'ticket_status', // the taxonomy
			array(
				'description'=> 'Closed Ticket',
				'slug' => 'closed',
				'parent'=> $parent_term['term_id']  // get numeric term id
			)
		);
	
		
	}
	add_action( 'init', 'create_ticket_status_taxonomy', 0 );
	
}

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
		'taxonomies'            => array( 'ticket_status' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-heart',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'tmm_support_ticket', $args );

}
add_action( 'init', 'create_tmm_support_ticket_cpt', 0 );

}



/* 
	When a comment is left on a post, update the post's date to that date - 
	then we'll be able to sort by most-recently commented-on.
	via https://wordpress.stackexchange.com/a/154506
*/
function update_post_modified_date(  $comment_id, $comment ) {
	wp_update_post( array( 'ID' => $comment->comment_post_ID ) );
}
add_action( 'wp_insert_comment', 'update_post_modified_date', 10, 2 );

// Create function that displays all of the currently-logged-in user's tmm_support_ticket posts on the "My Account" page
function display_customer_support_tickets() {
	global $current_user;
    get_currentuserinfo();
    
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	// WP_Query arguments
	$args = array (
		'post_type'              => array( 'tmm_support_ticket' ),
		'author'                 => $current_user->ID,
		'paged'					 => $paged,
		'posts_per_page'         => '10',
		'posts_per_archive_page' => '10',
		'orderby' => array( 
			'modified'	=> 'DESC',
		),
	);
	
	// The Query
	$query = new WP_Query( $args );
	
	/* 
	//Test what's in the $query
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<pre>';
			var_dump($query);
			echo '</pre>';
		}
	} 
	*/
	
	// Restore original Post Data
	wp_reset_postdata();?>
	<div class="my_support_tickets">
		<h2>My Support Tickets</h2>
		<table class="shop_table shop_table_responsive my_account_subscriptions my_account_orders">
			<thead>
				<tr>
					<th class="subscription-id order-number"><span class="nobr">Support Ticket Title</span></th>
					<th class="subscription-status order-status"><span class="nobr">Last Activity Date</span></th>
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
						/*
							echo '<pre>';
							print_r($comments);
							echo '</pre>';
						*/
						foreach($comments as $comment) :
							$comment_author = $comment->comment_author;
							$comment_date = date("D M j, Y \a\t g:i A", strtotime($comment->comment_date));
							$comment_content = substr($comment->comment_content, 0, 50) . '...';
							$comment_ID = $comment->comment_ID;
							$comment_post_ID = $comment->comment_post_ID;
						endforeach;
						?>
						<?php 
							$ticket_statuses = get_the_terms( $post->ID, 'ticket_status' );
							foreach($ticket_statuses as $ticket_status) {
								$ticket_status = $ticket_status->name; 
							}
						?>
						<tr class="order<?php if ( $ticket_status == 'Active' ) { echo ' active'; } else { echo ' closed'; } ?>">
							<td class="support-ticket-title">
								<a href="<?php the_permalink(); ?>" title="<?php the_excerpt(); ?>"><?php the_title(); ?></a> <?php edit_post_link('edit','[',']'); ?>
							</td>
							<td class="support-ticket-last-activity-date">
								<?php if (!$comments) {
									echo 'Request submitted on ' . get_the_date('D M j, Y \a\t g:i A') . ' by ' . get_the_author_meta( 'user_firstname', $post->post_author ) . ' ' . get_the_author_meta( 'user_lastname', $post->post_author );
								} else { ?>
									<a href="<?php echo get_permalink($comment_post_ID); ?>#comment-<?php echo $comment_ID; ?>" title="By <?php echo $comment_author; ?>: <?php echo $comment_content; ?>"><?php echo $comment_content; ?></a><?php echo ' on ' . $comment_date . ' by ' . $comment_author;
								} ?>
								
							</td>
							<td class="support-ticket-status data-title="Next Payment">
								<?php the_terms( $post->ID, 'ticket_status', '', ' / ' ); ?>
							</td>
						</tr>
						<?php 
					}
 
					//Support Ticket Pagination (currently BROKEN)
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
	
	echo '<div style="margin-bottom:1em;padding:1em;border:3px solid #000;border-radius:10px;">' . do_shortcode ( '[gravityform id="1" title="true" description="true" ajax="false"]' ) . '</div>';
}
add_action ('woocommerce_before_my_account', 'display_customer_support_tickets');