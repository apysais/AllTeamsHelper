<?php
/**
 * This is for the displaying menu and siblings
 * 
 * 
 * @since 0.0.1
 * */
class AllT_NavMenuChildrenShortcode{
	/**
	 * instance of this class
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var	null
	 * */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	public function allteams_get_menu_items($atts){
		global $posts;
		if( is_page() ){
			$wp_menus = array();
			$posts_id = $posts[0]->ID;
			$posts_title = $posts[0]->post_title;
			
			$parse_menus = array();
			$menu_id = null;
			
			$a = shortcode_atts( array(
				'menu_name' => ''
			), $atts );
			$parent_arr = array();
			$allteam_nav_menu = new AllT_NavMenuChildren;
			$wp_menus = wp_get_nav_menu_items($a['menu_name']);
			$menu_id = $allteam_nav_menu->allteam_search_posts_inmenu($posts_id, $wp_menus);
			if( !empty($wp_menus)){
				foreach($wp_menus as $k => $v){
					if( $v->ID == $menu_id ){
						$parent_arr = $v;
					}
				}
			}
			$parse_menus = $allteam_nav_menu->allteam_parse_menu($a['menu_name']);

			if( !is_null($menu_id) && $menu_id != 0 ){
				echo $allteam_nav_menu->allteam_nav_menu_html($parse_menus->$menu_id, $parent_arr);
			}
		}
	}
	
	public function __construct(){
		add_shortcode( 'allteams_menu_items_children', array($this, 'allteams_get_menu_items') );
	}
}
