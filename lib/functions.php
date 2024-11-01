<?php

function showrandompost_checkVar($var) {
	$check = false;

	if (isset($var)) {
		if (!empty($var)) {
			$check = true;
		}
	}
	return $check;
}

// Genera un Ramdom String

function showrandompost_generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function showrandompost_userRandomString($length = 4) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function showrandompost_clean_scripts($url) {
	$urlclean = preg_replace('/((\%3C)|(\&lt;)|<)(script\b)[^>]*((\%3E)|(\&gt;)|>)(.*?)((\%3C)|(\&lt;)|<)(\/script)((\%3E)|(\&gt;)|>)|((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/is', "", $url);
	return $urlclean;
}


function showrandompost_create_widget() {
	include_once plugin_dir_path(__FILE__) . 'widget.php';
	register_widget('showrandompost_widget');
}
add_action('widgets_init', 'showrandompost_create_widget');

add_action('wp_enqueue_scripts', 'showrandompost_script_enqueuer');

function showrandompost_script_enqueuer() {
				$dummy = new showrandompost_widget();
				$settings = $dummy->get_settings();
				wp_register_script("my_ajax_script", plugins_url('my_ajax_script.js', __FILE__), array('jquery'));
				wp_localize_script('my_ajax_script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'timeexec' => $settings[2]["showrandompost_time"]));
				wp_enqueue_script('jquery');
				wp_enqueue_script('my_ajax_script');
}

add_action("wp_ajax_nopriv_my_ajax_post", "my_ajax_post");
add_action("wp_ajax_my_ajax_post", "my_ajax_post");

function my_ajax_post() {
				$actualid = $_REQUEST['actualid'];
				$cats = base64_decode($_REQUEST['cat']);
				//$cats = json_decode($cats);
				$ids = get_all_published_ids($cats);
				$i = array_rand($ids, 1);
				$postid = $ids[$i];
		        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($postid), 'thumbnail_size' );
				$thumbnail = $thumb['0'];
				if(!isset($thumbnail)) $thumbnail = plugins_url('../img/noimage.png',__FILE__);
		        $title = apply_filters('the_title', get_post_field('post_title', $postid));
		        $content = get_the_excerpt_byid($postid);
		        $permalink = post_permalink($postid);

				if ($thumbnail === false || $title === false || $content === false) {
					$result['type'] = "error";
				} else {
					$result['type'] = "success";
					$result['thumbnail'] = $thumbnail;
					$result['titulo'] = $title;
					$result['contenido'] = $content;
					$result['permalink'] = $permalink;
					$result['postid'] = $postid;
				} 

				if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$result = json_encode($result);
					echo $result;
				} else {
					wp_redirect($_SERVER["HTTP_REFERER"]);
				}

				wp_die();

}

function get_the_excerpt_byid( $post_id ){
				  global $post;  
				  $save_post = $post;
				  $post = get_post($post_id);
				  setup_postdata( $post ); // hello
				  $output = the_excerpt_published_length(75);
				  $post = $save_post;
				  return $output;
}

function get_all_published_ids($cat) {
				$args = array('post_status' => 'publish', 'posts_per_page' => -1);
					preg_match_all('!\d+!', $cat, $matches);
					$args["category__in"] = $matches[0];
				query_posts( $args );

				while ( have_posts() ) : the_post();
					$ids[] = get_the_ID();
				endwhile;

				wp_reset_query();

			return $ids;
}

function the_excerpt_published_length($charlength) {
	$excerpt = get_the_excerpt();
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$text = mb_substr( $subex, 0, $excut );
		} else {
			$text = $subex;
		}
		$text .= '[...]';
	} else {
		$text = $excerpt;
	}

	return $text;
}