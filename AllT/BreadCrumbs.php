<?php
/**
 * This is for the displaying menu and siblings
 * 
 * 
 * @since 0.0.1
 * */
class AllT_BreadCrumbs{
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
	
	public function menu($theme_locations, $parent_home = '#', $site_home = null){
		global $posts;
		$breadcrumb_items = array();
		$posts_id = $posts[0]->ID;
		$posts_title = $posts[0]->post_title;
  
		$locations = get_nav_menu_locations();
		if( isset($locations[$theme_locations]) ){
			$menu = get_term( $locations[$theme_locations], 'nav_menu' );

			$allteam_nav_menu = new AllT_NavMenuChildren;
			$wp_menus = wp_get_nav_menu_items($menu->term_id);

			$menu_id = $allteam_nav_menu->allteam_search_posts_inmenu($posts_id, $wp_menus);

			$parse_menus = $allteam_nav_menu->allteam_parse_menu($menu->name);

			if( !empty($parse_menus->$menu_id) ){
				$parent_arr = array();
				if( !empty($wp_menus)){
					foreach($wp_menus as $k => $v){
						if( $v->ID == $menu_id ){
							$parent_arr = $v;
						}
					}
				}

				echo '<ol class="breadcrumb allteam-helper-breadcrumb">';
					echo '<li class="breadcrumb-item">';
						if( !is_multisite() ){
							$parent_home = get_bloginfo('url');
						}
						echo '<a href="'.$parent_home.'">';
						echo 'Home';
						echo '</a>';
					echo '</li>';
					if( is_multisite() ){
						echo '<li class="breadcrumb-item">';
							echo '<a href="'.get_bloginfo('url').'">';
							echo get_bloginfo('name');
							echo '</a>';
						echo '</li>';
					}
					if( !empty($parent_arr) && $posts_id != $parent_arr->object_id ){
						echo '<li class="breadcrumb-item breadcrumb-post-id-'.$parent_arr->object_id.'">';
						echo '<a href="'.get_permalink($parent_arr->object_id).'">';
							echo $parent_arr->title;
						echo '</a>';
						echo '</li>';
					}
				if( !empty($parse_menus->$menu_id) ){
					$stop = false;
					foreach($parse_menus->$menu_id as $k => $v){
						$active = ($posts_id == $v->id) ? 'active':'';
						if( $active == 'active' ){
							echo '<li class="breadcrumb-item breadcrumb-post-id-'.$v->id.' '.$active.'">';
							echo $v->title;
							$stop = true;
							echo '</li>';
						}
					}
				
				}
				echo '</ol>';
			}
		}
		
	}
		
	public function __construct(){}
}

