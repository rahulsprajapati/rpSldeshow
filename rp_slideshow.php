<?php

/**
 * Plugin Name: RP_Slideshow
 * Plugin URI: http://developerrahul.com/
 * Description: Slideshow Images
 * Version: 1.0.0
 * Author: Rahul Prajapati
 * Author URI: http://resume.developerrahul.com/
 * Text Domain: http://resume.developerrahul.com/
 * License: GPL2
 */

/*
 * Copyright 2014 - 2015 Rahul Prajapati (email : com.developer.rahul@gmail.com)
 */
defined ( 'ABSPATH' ) or die ( "No script kiddies please!" );

// Admin rpSlideshow Setting Menu
add_action ( 'admin_menu', 'rp_slideshow' );
function rp_slideshow() {
	global $post;
	global $rp_slideshow_hook;
	
	// Add Option in Setting Menu
	$rp_slideshow_hook = add_options_page ( 'RP_Slideshow Settings', 'RP_Slideshow Settings', 'manage_options', 'rp_slideshow', 'rp_slideshow_menu' );
}

// Add Option in Setting Menu
function rp_slideshow_menu() {
	?>
<div class="rp_loader">
	<img src="<?php echo plugins_url('/css/loader.GIF', __FILE__); ?>" />
</div>
<div class="rp_slideshow_admin rp_container">
	<h2>Upload Images (shortcode : [rp_Slideshow])</h2>
	<span><strong>Note : </strong>Slide Image Minimum Size => width = 640px
		and height = 340px</span>
	<hr />
	<form action="" id="form1" class="rp_left" method="POST"
		name="media_upload" enctype="multipart/form-data">
		<input type="hidden" name="action" id="action" value="rp_ajax_upload" />
		Slide Name : <input type="text" class="rp_text_box" name="title_name" />
		<input type="button" name="UploadImage" id="uploadImage"
			class="rp_btn" value="Upload Image" /> <span id="imageSrc">No Image
			Is Selected</span> <input type="file" name="url" id="slide1" /> <input
			type="submit" name="Upload" id="upload" class="rp_btn"
			value="Add Slide" />
	</form>
	<input type="button" name="change_order" id="change_order"
		class="rp_btn rp_right" value="Change Slide Order" /> <input
		type="button" name="save" id="save" class="rp_btn rp_right"
		value="Save Slideshow" />
	<div class="rp_clear"></div>
	<hr />
	<div class="rp_msgBox"></div>
	<div class="rp_clear"></div>
	<div class="table connectedSortable  rp_block" id="table"></div>
</div>
<?php
}

// Add Scripts rpSlideshow hook
add_action ( 'admin_enqueue_scripts', 'rp_slideshow_script' );
function rp_slideshow_script($hook) {
	global $rp_slideshow_hook;
	if ($hook != $rp_slideshow_hook) {
		return;
	}
	all_scripts ();
}
function all_scripts() {
	wp_enqueue_style ( 'rp_images_style', plugins_url ( '/css/rp_images_style.css', __FILE__ ), array (), null, 'all' );
	
	wp_enqueue_script ( 'rpSlideshow_jquery', plugin_dir_url ( __FILE__ ) . 'js/jquery-1.10.2.js', array (
			'jquery' 
	), '1.0.0', false );
	wp_enqueue_script ( 'rpSlideshow_jquery_ui', plugin_dir_url ( __FILE__ ) . 'js/jquery-ui.js', array (
			'rpSlideshow_jquery' 
	), '1.0.0', false );
	wp_enqueue_script ( 'rpSlideshow_script', plugin_dir_url ( __FILE__ ) . 'js/script.js', array (
			'rpSlideshow_jquery_ui' 
	), '1.0.0', false );
	wp_enqueue_script ( 'rpSlideshow_ajax_script', plugin_dir_url ( __FILE__ ) . 'js/ajax_script.js', array (
			'rpSlideshow_script' 
	), '1.0.0', true );
}

add_action ( 'wp_ajax_rp_ajax_upload', 'ajax_rp_ajax_upload' );
function ajax_rp_ajax_upload() {
	$title_name = $_POST ["title_name"];
	$image = $_FILES ['url'];
	$getimagesize = getimagesize ( $_FILES ['url'] ['tmp_name'] );
	// echo "Width = ".$getimagesize[0] ."Hei = ". $getimagesize[1];
	
	if ($getimagesize [0] >= 640 && $getimagesize [1] >= 340) {
		$upload_overrides = array (
				'test_form' => false 
		);
		
		$movefile = wp_handle_upload ( $image, $upload_overrides );
		
		if ($movefile) {
			
			$my_post = array ();
			$my_post ['post_title'] = $title_name;
			$my_post ['post_content'] = $movefile ['url'];
			$my_post ['guid'] = $movefile ['url'];
			$my_post ['post_mime_type'] = $movefile ['type'];
			$my_post ['post_status'] = 'publish';
			$my_post ['post_type'] = 'rp_slideshow';
			
			$id = wp_insert_post ( $my_post );
			
			$order = get_option ( 'rpslideshow_display_order' );
			$order = getOrder($order,$id);
			
			update_option ( 'rpslideshow_display_order', $order );
			
			echo "0";
		} else {
			echo "1";
		}
	} else {
		echo "2";
	}
	
	die ();
}

function getOrder($order,$id){
	if ($order != "") {
		$order = $order . "," . $id;
	} else {
		$order = $order . $id;
	}
	return $order;
}

add_action ( 'wp_ajax_rpslideshow_ajax_images_list', 'rpslideshow_ajax_images_list' );
function rpslideshow_ajax_images_list() {
	$order = get_option ( 'rpslideshow_display_order' );
	
	if ($order != "") {
		$order = explode ( ',', $order );
		
		for($i = 0; $i < count ( $order ); $i ++) {
			
			$rpslideshow_post = get_post ( $order [$i] );
			
			?>

<div class="row rp_image_row rp_inline_block"
	id='<?php echo "ID_".$order[$i]; ?>'>

	<label class="rp_left rp_slide_label">Slide<?php echo $i+1;?></label> <input
		type="button" onclick="deleteImage(<?php echo $order[$i];?>)"
		name="delete_image" class="rp_btn rp_delete_btn" value="X" />
	<div class="rp_clear"></div>
	<hr class="rp_hr" />
	<div class="column rp_block">
		<div class="wrap">
			<img class="fake" src="<?php echo $rpslideshow_post->post_content;?>" />
			<div class="img_wrap">
				<img class="normal"
					src="<?php echo $rpslideshow_post->post_content;?>" />
			</div>
		</div>
	</div>


	<div class="column rp_block rp_top">
		<label class="rp_center"><h3 class="rp_no_margin"><?php echo $rpslideshow_post->post_title; ?></h3></label>
		<input type="hidden" name="id" value="1"> <input type="hidden"
			name="order" value="1">
	</div>

</div>

<?php
		}
	}
	
	die ();
}

add_action ( 'wp_ajax_rpslideshow_ajax_update_order', 'rpslideshow_ajax_update_order' );
function rpslideshow_ajax_update_order() {
	$newOrder = $_POST ['ID'];
	
	$displayorder = "";
	
	for($i = 0; $i < count ( $newOrder ); $i ++) {
		$displayorder = getOrder($displayorder,$newOrder [$i]);
		
	}
	update_option ( 'rpslideshow_display_order', $displayorder );
	
	die ();
}

add_shortcode ( 'rp_Slideshow', 'rp_Slideshow_Shortcode' );
function rp_Slideshow_Shortcode() {
	all_scripts ();
	wp_enqueue_style ( 'rpSlideshow_bootstrap_style', plugins_url ( '/css/bootstrap.min.css', __FILE__ ), array (), null, 'all' );
	
	wp_enqueue_script ( 'rpSlideshow_bootstrap_jq_script', plugin_dir_url ( __FILE__ ) . 'js/jquery.min.js', array (
			'rpSlideshow_script' 
	), '1.0.0', false );
	wp_enqueue_script ( 'rpSlideshow_bootstrap_script', plugin_dir_url ( __FILE__ ) . 'js/bootstrap.min.js', array (
			'rpSlideshow_bootstrap_jq_script' 
	), '1.0.0', false );
	
	$order = get_option ( 'rpslideshow_display_order' );
	
	$slideshow = '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">  
   							<ol class="carousel-indicators">';
	if ($order != "") {
		$order = explode ( ',', $order );
		
		for($i = 0; $i < count ( $order ); $i ++) {
			if ($i == 0) {
				$slideshow .= '<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>';
			} else {
				$slideshow .= '<li data-target="#carousel-example-generic" data-slide-to="' . $i . '"></li>';
			}
		}
	}
	$slideshow .= '</ol>
							<div class="carousel-inner" role="listbox">';
	$order = get_option ( 'rpslideshow_display_order' );
	if ($order != "") {
		$order = explode ( ',', $order );
		
		for($i = 0; $i < count ( $order ); $i ++) {
			$rpslideshow_post = get_post ( $order [$i] );
			if ($i == 0) {
				$slideshow .= '<div class="item active">
      																<img src="' . $rpslideshow_post->post_content . '" alt="">
      																<div class="carousel-caption">
	
	        														</div>
    														</div>';
			} else {
				$slideshow .= '<div class="item">
																	<img src="' . $rpslideshow_post->post_content . '" alt="">
      																<div class="carousel-caption">       
      																</div>
    															</div>';
			}
		}
	}
	$slideshow .= '
							</div>
  							<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    								<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    								<span class="sr-only">Previous</span>
  							</a>
  							<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    								<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    								<span class="sr-only">Next</span>
  							</a>
				</div>';
	
	return $slideshow;
}

add_action ( 'wp_ajax_rpslideshow_delete_image', 'rpslideshow_delete_imagePost' );
function rpslideshow_delete_imagePost() {
	$postId = $_POST ['id'];
	$order = get_option ( 'rpslideshow_display_order' );
	$updateorder = "";
	
	if ($order != "") {
		$order = explode ( ',', $order );
		for($i = 0; $i < count ( $order ); $i ++) {
			if ($order [$i] != $postId) {
				$updateorder = getOrder($updateorder,$order [$i]);
			}
		}
	}
	
	update_option ( 'rpslideshow_display_order', $updateorder );
	wp_delete_post ( $postId );
	echo "Post Order => " . $updateorder;
	
	die ();
}

?>