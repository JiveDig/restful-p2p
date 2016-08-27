Restful P2P
===================

This plugin requires Posts 2 Posts and WP-API (v2) plugins for WordPress

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