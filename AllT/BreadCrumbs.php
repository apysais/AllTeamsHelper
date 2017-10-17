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
			$html = '';
			if( !empty($parse_menus->$menu_id) ){
				$parent_arr = array();
				if( !empty($wp_menus)){
					foreach($wp_menus as $k => $v){
						if( $v->ID == $menu_id ){
							$parent_arr = $v;
						}
					}
				}
				//_dump($parent_arr);
				$html .= '<ol class="breadcrumb allteam-helper-breadcrumb">';
					$html .= '<li class="breadcrumb-item">';
						if( !is_multisite() ){
							$parent_home = get_bloginfo('url');
						}
						$parent_title = 'Home';
						$parent_home = apply_filters('allteams_breadcrumb_parent_home', $parent_home);
						$parent_title = apply_filters('allteams_breadcrumb_parent_home', $parent_title);
						$html .= '<a href="'.$parent_home.'">';
						$html .= $parent_title;
						$html .= '</a>';
					$html .= '</li>';
					if ( is_multisite() ) {
						$html .= '<li class="breadcrumb-item">';
							$html .= '<a href="'.get_bloginfo('url').'">';
							$html .= get_bloginfo('name');
							$html .= '</a>';
						$html .= '</li>';
					}
					if( !empty($parent_arr) && $posts_id != $parent_arr->object_id ){
						$parent_arr = apply_filters('allteams_breadcrumb_parentarr', $parent_arr, $parse_menus, $posts_id);
						$html .= '<li class="breadcrumb-item breadcrumb-post-id-'.$parent_arr->object_id.' '.$parent_arr->object.' '.$parent_arr->type.' '.implode(' ',$parent_arr->classes).'">';
						$html .= '<a href="'.$parent_arr->url.'">';
							$html .= $parent_arr->title;
						$html .= '</a>';
						$html .= '</li>';
					}
				if( !empty($parse_menus->$menu_id) ){
					$stop = false;
					foreach($parse_menus->$menu_id as $k => $v){
						//_dump($v);
						$active = ($posts_id == $v->id) ? 'active':'';
						if( $active == 'active' ){
							$html .= '<li class="breadcrumb-item breadcrumb-post-id-'.$v->id.' '.$active.'">';
							$html .= $v->title;
							$stop = true;
							$html .= '</li>';
						}
					}
				
				}
				$html .= '</ol>';
				$html = apply_filters('allteams_breadcrumb_output', $html, $parse_menus, $parent_arr, $wp_menus);
				echo $html;
			}
		}
		
	}
	
	public function __construct(){}
}

