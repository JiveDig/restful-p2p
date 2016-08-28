Restful P2P
===================

Connect 2 objects (via Posts 2 Posts) with the Rest API (AJAX)

This plugin requires Posts 2 Posts and WP-API (v2) plugins for WordPress

## Basic Usage
```
/**
 * Display a Posts 2 Posts connection button
 *
 * @param  array  $args {
 *     @type string   name       Posts 2 Posts connection name
 *     @type integer  from       Posts 2 Posts 'from' connection ID
 *     @type integer  to         Posts 2 Posts 'to' connection ID
 *     @type string   connect    Connection button text to display if 2 objects are not connected
 *     @type string   connected  Connection button text to display if 2 objects are already connected
 *	   @type string   loading    Text to display while connection is being made
 * }
 *
 * @return	void
 */
$args = array(
    'name'      => 'users_to_pages',
    'from'      => get_current_user_id(),
    'to'        => get_the_ID(),
    'connect'   => 'Connect',
    'connected' => 'Connected!',
    'loading'   => 'Loading...',
);
echo get_restful_p2p_link( $args );
```

## Instructions

1. Install and activate required [Posts 2 Posts](https://wordpress.org/plugins/posts-to-posts/) plugin and [WordPress REST API (Version 2)](https://wordpress.org/plugins/rest-api/)
1. Install and activate [Restful P2P plugin](https://github.com/JiveDig/restful-p2p/)
1. Register your Posts 2 Posts connection(s) via its API

	Example:
	```
	add_action( 'p2p_init', 'prefix_register_p2p_connections' );
	function prefix_register_p2p_connections() {
	    p2p_register_connection_type( array(
			'name'		=> 'users_to_pages',
			'from'		=> 'user',
			'to'		=> 'page',
	    ) );
	}
	```
1. Use the helper function to add a Restful P2P 'connection button' anywhere you'd like

	Example:
	```
	add_filter( 'the_content', 'prefix_filter_the_content' );
	function prefix_filter_the_content( $content ) {

	    if ( ! function_exists('get_restful_p2p_link') ) {
	        return $content;
	    }

	    if ( ! is_singular('page') || ! is_main_query() ) {
	        return $content;
	    }

	    $args = array(
	        'name'      => 'users_to_pages',
	        'from'      => get_current_user_id(),
	        'to'        => get_the_ID(),
	        'connect'   => 'Do this',
	        'connected' => 'Done!',
	        'loading'   => 'Loading...',
	    );
	    $button = get_restful_p2p_link( $args );

	    return $content . $button;
	}
	```
1. As of now, clicking a button creates a connection, then clicking again deletes (disconnects) the connection. In the future I may add the ability to disable 'disconnecting' connections, I'm just not sure how many use cases there are for that sort of thing.