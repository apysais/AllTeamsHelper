<?php
/**
 * This is for the displaying menu and siblings
 *
 *
 * @since 0.0.1
 * */
class AllT_NavMenuChildren{
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

	/**
	 * prase the wp_get_nav_menu_items
	 *
	 * @param	$menu_name	string	the name of the menu nav
	 * @param	$return	string	the default return of the parse, default is object, can be assoc array
	 * - OBJ for object
	 * - ASSOC for associative array
	 *
	 * @return	base on $return
	 * */
	public function allteam_parse_menu($menu_name, $return = 'OBJ'){
		$parse_menu = array();
		$menus = wp_get_nav_menu_items($menu_name);
		//_dump($menus);
		if( $menus ){
			foreach($menus as $k => $v) {
				$posts_id = $v->object_id;
				$menu_id = $v->ID;
				$menu_parent_id = $v->menu_item_parent;
				$posts_title = $v->title;
				$key_id = $menu_id;
				if( $menu_parent_id != 0 ){
					$key_id = $menu_parent_id;
				}
				$parse_menu[$key_id][] = array(
					'id' => $posts_id,
					'title' => $posts_title,
					'menu_id' => $menu_id,
					'menu_parent_id' => $menu_parent_id,
					'posts_title' => $posts_title,
					'key_id' => $key_id,
					'object_id' => $v->object_id,
					'object' => $v->object,
					'type' => $v->type,
					'classes' => $v->classes,
					'custom_url' => $v->url,
					'url' => get_permalink($v->object_id),
				);
			}
		}
		if( $return == 'OBJ' ){
			return json_decode(json_encode($parse_menu), FALSE);
		}
		return $parse_menu;
	}

	/**
	 * search posts inside the menu
	 *
	 * @param	$search_by	mix
	 * this can be ID or title, default would be ID
	 * @param	$menu_object	wp_get_nav_menu_items
	 * @param	$ret	string	the default return of the parse, default is object, can be assoc array
	 * - OBJ for object
	 * - ASSOC for associative array
	 *
	 * @return	array | object
	 * */
	public function allteam_search_posts_inmenu($search_by, $menu_object){
		$posts_id = null;
		$posts_title = null;
		$search_posts_menu_array = array();
		$search_menu_parent_id = null;
		$search_menu_id = null;
		$menu_parent_id = 0;
		/**
		 * must be ID
		 * */
		if( is_int($search_by) ){
			$posts_id = $search_by;
		}elseif( is_string($search_by) ){
			/**
			 * then its title
			 * */
			$posts_title = trim(strtolower($search_by));
		}

		if( !empty($menu_object) ){
			//$menu_parent_id = 0;
			foreach($menu_object as $k => $v){
				$search_post_title = '';
				if( !is_null($posts_id) && $posts_id == $v->object_id ){
					$menu_parent_id = $v->menu_item_parent == 0 ? $v->ID:$v->menu_item_parent;
				}elseif( !is_null($posts_title) && trim(strtolower($v->title)) == $posts_title ){
					$menu_parent_id = $v->menu_item_parent == 0 ? $v->ID:$v->menu_item_parent;
				}
			}
		}
		return $menu_parent_id;
	}

	public function init(){

	}

	/**
	 * this is for the html stuff
	 * */
	public function allteam_nav_menu_html($menu_array, $parent_arr, $menu_class_array = array('allteam-navmenu')){
		global $posts;
		$post = $posts[0];
		$html = '';
		$menu_class = implode(' ', $menu_class_array);
		if( is_page() && $menu_array && count($menu_array) > 0 ){
			$html = '<div id="shortcode-allteam-page-menu" class="'.$menu_class.'">';
				$current_menu = '';

				if( count($menu_array) > 1 ){
					$html .= '<h3 class="parent-'.$parent_arr->menu_id.' shortcode-allteam-page-menu-title">'.$parent_arr->title.'</h3>';
				}
				$html .= '<ul class="menu-children">';
				foreach($menu_array as $k => $v){
					$current_menu = '';
					if( $post->ID == $v->id ){
						$current_menu = 'current-item';
					}
					if( $parent_arr->title != $v->title ) {
						$html .= '<li class="'.$current_menu.'">';
							$html .= '<a href="'.get_permalink($v->id).'">';
							$html .= '<span class="parent-menu menu-item-'.$v->menu_id.'">'.$v->title.'</span>';
							$html .= '</a>';
						$html .= '</li>';
					}
				}
				$html .= '</ul>';
			$html .= '</div>';
		}
		return $html;
	}
	public function allteam_nav_menu_children_html($menu_array, $menu_class_array = array('allteam-navmenu-children')){
		global $posts;
		$post = $posts[0];
		$html = '';
		$menu_class = implode(' ', $menu_class_array);

		if( is_page() && $menu_array && count($menu_array) > 0 ){
			$html = '<div id="shortcode-allteam-page-menu-children" class="'.$menu_class.'">';
				$current_menu = '';

				$html .= '<ul class="menu-children">';
				foreach($menu_array as $k => $v){
					$html .= '<li>';
						$html .= '<a href="'.$v->url.'">';
						$html .= '<span class="parent-menu menu-item-'.$v->menu_id.'">'.$v->title.'</span>';
						$html .= '</a>';
					$html .= '</li>';
				}
				$html .= '</ul>';
			$html .= '</div>';
		}
		return $html;
	}
	public function __construct(){}
}
