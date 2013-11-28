<?php
#-----------------------------------------
#	RT-Theme theme_functions.php
#	version: 1.0
#-----------------------------------------

	
#
#	sub page layouts function
# 
 
function sub_page_layout($location,$sidebar){
	
	$sidebar = (!$sidebar) ? sidebar_location() : $sidebar;
	$page_layout = ($sidebar=="full") ? "fullwidth" : "sidebarwidth";
		
	$content_location = ($sidebar =="left") ? $content_location="right" : $content_location="left";
	
	if($location=="subheader"){
		echo '<div id="main" role="main" class="clearfix">';
		echo	'<div class="sub_page '.$page_layout.' ">';
		echo ($page_layout=="sidebarwidth") ? '<div class="content '.$content_location.' clearfix">' : '<div class="content '.$page_layout.' clearfix">';
	}

	if($location=="subfooter"){
		echo '</div>'; 

		if($page_layout=="sidebarwidth"){
			echo '<div class="sidebar '.$sidebar.' clearfix">';
				get_template_part( 'sidebar', 'sidebar-core-file');
			echo '</div>';
		} 
		
		echo '</div></div>';
	} 

}

#
#	optional sidebar locations
#

function sidebar_location(){ 
	if(is_page() || is_single()) global $post; 
 
	$sidebar 	= get_option(THEMESLUG.'_sidebar_position'); //no specific sidebar location - use default
	$metabox_selection  = (isset($post)) ? get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) : false ; //sidebar selection via metaboxes
	
	
	// site part = regular pages
	if(is_theme_page() || is_contact_page() ) $sidebar = (get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true)) ? get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) : $sidebar ; //sidebar location regular page 	
	
	// site part = blog
	if(is_blog_page()) $sidebar  = ($metabox_selection ? $metabox_selection : ( get_option(THEMESLUG.'_sidebar_position_blog') ? get_option(THEMESLUG.'_sidebar_position_blog') : $sidebar ) );	 

	// site part = product
	if(is_product_page()) $sidebar  = ($metabox_selection	? $metabox_selection : (	get_option(THEMESLUG.'_sidebar_position_product') ? get_option(THEMESLUG.'_sidebar_position_product') : $sidebar ) );	  

	// site part = portfolio
	if(is_portfolio_page()) $sidebar  = ($metabox_selection  ? $metabox_selection : (	get_option(THEMESLUG.'_sidebar_position_portfolio') ? get_option(THEMESLUG.'_sidebar_position_portfolio') : $sidebar ) );
 
	return $sidebar;
}

 
 
#
#	Get Page Layout
#

function get_page_layout(){ 
	if(is_page() || is_single()) global $post; 
	
	$metabox_selection =  ($post) ? ( get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) )  ?  ( get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) )  : false : false; //fullwidth selection via metaboxes

	// site part = regular pages
	if(is_theme_page() || is_contact_page()) $fullwidth = $metabox_selection; 
	
	// site part = blog
	if(is_blog_page()) $fullwidth = ($metabox_selection) ?  ($metabox_selection=="full" ? "fullwidth" : false )	: get_option(THEMESLUG.'_sidebar_position_blog')=="full" ? "fullwidth" : false;		
		
	return $fullwidth;
}

 
 
 

#
# Add Class WP Menu - adds class for the first menu item
#
 
function add_class_first_item($menu){
	
	$find="\"><a ";
	$replace=" first\"><a ";
	return preg_replace('/'.$find.'/', $replace, $menu, 1); 
}


#
# Remove more link in excerpts 
#

function no_excerpt_more($more) {
	return '.. ';
}

#
# Get page count
#

function get_page_count(){
    global $wp_query;	
    $count=array('page_count'=>$wp_query->max_num_pages,'post_count'=>$wp_query->post_count);
    return $count;
}


#
# Pagination
#
function get_pagination($range = 7){
	global $paged, $wp_query;
	
	$max_page = $wp_query->max_num_pages;
	 
	if($max_page > 1){
	if(!$paged){
	  $paged = 1;
	}

	if ($paged > 1){
		echo "<li class=\"arrowleft\">";
		    previous_posts_link('←');
		echo "</li>\n";
	}
	if($max_page > $range){
	if($paged < $range){
	  for($i = 1; $i <= ($range + 1); $i++){
		echo "<li";
		if($i==$paged) echo " class='active'";
		echo "><a href='" . get_pagenum_link($i) ."'>$i</a>";
		echo "</li>\n";
	  }
	}
	elseif($paged >= ($max_page - ceil(($range/2)))){
	  for($i = $max_page - $range; $i <= $max_page; $i++){
		echo "<li";
		if($i==$paged) echo " class='active'";
		echo "><a href='" . get_pagenum_link($i) ."'>$i</a>";
		echo "</li>\n";
	  }
	}
	elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
	  for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
	    echo "<li";
	    if($i==$paged) echo " class='active'";
	    echo "><a href='" . get_pagenum_link($i) ."'>$i</a>";
	    echo "</li>\n";
	  }
	}
	}
	else{
	for($i = 1; $i <= $max_page; $i++){
	    echo "<li";
	    if($i==$paged) echo " class=\"active\" ";
	    echo "><a href='" . get_pagenum_link($i) ."'>$i</a>";
	    echo "</li>\n";
	}
	}
	if ($paged != $max_page){
	    echo "<li class=\"arrowright\">";
	    next_posts_link('→');
	    echo "</li>\n";
	}
	
	}
}


#
# checks page reserved for blog product or portfolio
# 

function is_theme_page(){
    global $post; 
    
    $post->ID = wpml_page_id($post->ID);
    
    if($post->ID != BLOGPAGE && $post->ID !=PRODUCTPAGE && $post->ID !=PORTFOLIOPAGE && $post->ID !=CONTACTPAGE  && is_page()){
	   return true;
    }
    
} 

#
# checks theme parts that reserved for blog
# 

function is_blog_page(){
    global $post; 
    
    $post->ID = wpml_page_id($post->ID);
    
	if($post->ID == BLOGPAGE || is_category() || is_single() && $post->post_type!='products' && $post->post_type!='portfolio'  ){
	    return true;
	} 

	//check default blog template usage
	if(get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) == "templateid_004" ){
	    return true;
	}			
}

#
# checks theme parts that reserved for products
# 

function is_product_page(){
    global $post,$taxonomy; 
    
    $post->ID = wpml_page_id($post->ID);
    
	if($post->ID == PRODUCTPAGE || $taxonomy=="product_categories" || $post->post_type=='products'){
	    return true;
	} 

	//check default product template usage
	if(get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) == "templateid_003" ){
	    return true;
	}
}

#
# checks theme parts that reserved for portfolio
# 

function is_portfolio_page(){
    global $post,$taxonomy,$sidebar; 
  
    $post->ID = wpml_page_id($post->ID);
    
	if($post->ID == PORTFOLIOPAGE || $taxonomy=="portfolio_categories" || $post->post_type=='portfolio'){
	    return true;
	}

	//check default porfolio template usage
	if(get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) == "templateid_002" ){
	    return true;
	}	
}

#
# checks theme parts that reserved for contact page
# 

function is_contact_page(){
    global $post; 
  
    $post->ID = wpml_page_id($post->ID);
    
	if($post->ID == CONTACTPAGE){
	    return true;
	}

	//check default contact template usage
	if(get_post_meta($post->ID, THEMESLUG.'custom_sidebar_position', true) == "templateid_005" ){
	    return true;
	}		
}

#
# gets orginal paths of images when multi site mode active
#
function find_image_org_path($image) {
	if(is_multisite()){
		global $blog_id;
		if (isset($blog_id) && $blog_id > 0) {
			if(strpos($image,get_bloginfo('wpurl'))!==false){//image is local
				$the_image_path = get_current_site(1)->path.str_replace(get_blog_option($blog_id,'fileupload_url'),get_blog_option($blog_id,'upload_path'),$image);
			}else{
				$the_image_path = $image;
			}
		}else{
			$the_image_path = $image;
		}
	}else{
		$the_image_path = $image;
	}
	
	return $the_image_path;
}


#
# set selected theme style to body tag
#

function rt_body_class_name($classes) {
	$classes[] = get_option( THEMESLUG."_style" );
	$classes[] = get_option( THEMESLUG."_boxed_design" ) ? 'boxed' : '' ;	
	// return the $classes array
	return $classes;
}

#
# returns a post ID from a url
#

function rt_get_attachment_id_from_src ($image_src) { 
	global $wpdb; 
	$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	$id    = $wpdb->get_var($query);
	return $id; 
}


#
# Exclude some custom post types from search
#
function excludePostsfromSearch($query) { 
	if ($query->is_search) { 
		$query->set('post_type', array("post","page","portfolio","products"));
		$query->set('posts_per_page', 10);
	}
	return $query; 
}
if(!is_admin()) add_filter('pre_get_posts','excludePostsfromSearch');

?>