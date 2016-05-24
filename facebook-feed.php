<?php

/*
Plugin Name: Custom Facebook Widget
Plugin URI: http://webjen.github.com
Description: A simple custom Facebook feed
Version: 1.0
Author: Jenny Ryd&eacute;n
Author URI: http://webbviken.se
License: GPL2
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Creating the widget, http://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
class pemp_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'pemp_widget',

		// Widget name will appear in UI
		__('My Facebook Feed', 'pemp_widget_domain'),

		// Widget description
		array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'pemp_widget_domain' ), )
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );
		$no_of_posts = apply_filters( 'widget_title', $instance['no_of_posts'] );
		$access_token = '';
		$fb_page_id = '';

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output

				$fb_page_id = "PeterErikssonMP";
				$profile_photo_src = "https://graph.facebook.com/{$fb_page_id}/picture?type=square";
				$access_token = "943966015717044|y-L_fvJZWuPZnUcRh9FHXcGH4tk";
				$fields = "id,message,picture,link,name,description,type,icon,created_time,from,object_id";
				$limit = $no_of_posts;

				$json_link = "https://graph.facebook.com/{$fb_page_id}/feed?access_token={$access_token}&fields={$fields}&limit={$limit}";
				$json = file_get_contents($json_link);

				$obj = json_decode($json, true);
				$feed_item_count = count($obj['data']);

				$description = '';

				for($x=0; $x<$feed_item_count; $x++){
				    // to get the post id
				    $id = $obj['data'][$x]['id'];
				    $post_id_arr = explode('_', $id);
				    $post_id = $post_id_arr[1];

				    // user's custom message
				    $message = $obj['data'][$x]['message'];

				    $picture = '';
					$link = '';

				    // picture from the link
				    if( !empty($obj['data'][$x]['picture']) ) {
				    	$picture = $obj['data'][$x]['picture'];
					    $picture_url_arr = explode('&url=', $picture);
					    $picture_url = urldecode( $picture );
				    }

				    // link posted
				    if( !empty( $obj['data'][$x]['link'] ) ) {
				    	$link = $obj['data'][$x]['link'];
					}

				    // name or title of the link posted
				    if( !empty( $obj['data'][$x]['name'] ) ) {
					    $name = $obj['data'][$x]['name'];
					}

				    if ( !empty( $obj['data'][$x]['description'] ) )
				    	$description = $obj['data'][$x]['description'];
				    $type = $obj['data'][$x]['type'];

				    // when it was posted
				    $created_time = $obj['data'][$x]['created_time'];
				    $converted_date_time = date( 'Y-m-d H:i:s', strtotime($created_time));
				    $ago_value = time_elapsed_string($converted_date_time);

				    // from
				    $page_name = $obj['data'][$x]['from']['name'];

				    // useful for photo
				    if ( !empty( $obj['data'][$x]['object_id'] ) )
				    	$object_id = $obj['data'][$x]['object_id'];

				    echo "<div class='row'>";

					    echo "<div class='col-md-4'>";

					        echo "<div class='profile-info'>";

					            echo "<div class='profile-photo'>";
					                echo "<img src='{$profile_photo_src}' alt='Peter Profilfoto' width='40' height='40'/>";
					            echo "</div>";

					            echo "<div class='profile-name'>";
					                echo "<div>";
					                    echo "<a href='https://fb.com/{$fb_page_id}' target='_blank'>{$page_name}</a> ";
					                    echo "shared a ";
					                    if($type=="status"){
					                        $link="https://www.facebook.com/{$fb_page_id}/posts/{$post_id}";
					                    }
					                    echo "<a href='{$link}' target='_blank'>{$type}</a>";
					                echo "</div>";
					                echo "<div class='time-ago'>{$ago_value}</div>";
					            echo "</div>";

					        echo "</div>";

					        echo "<div class='profile-message'>{$message}</div>";

					    echo "</div>";

						echo "<div class='col-md-8'>";
						    //echo "<a href='{$link}' target='_blank' class='post-link'>";

						        echo "<div class='post-content'>";

						            if( $type=="status" ){
						                echo "<div class='post-status'>";
						                    echo "Visa på Facebook";
						                echo "</div>";
						            }

						            else if( $type=="photo" ){
						                echo "<img src='https://graph.facebook.com/{$object_id}/picture' alt='Photo from Facebook' />";
						            }

						            else if ( $type=="video" ) {

						            	var_dump($link);

						            	if ( strpos($link,'facebook' ) !== false ) {
										    echo '<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook%2Fvideos%2F{$post_id}%2F&width=500&show_text=false&appId=943966015717044&height=281" width="500" height="281" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>';
										} elseif (strpos($link,'youtube' ) !== false) {
											//Make Youtube link into YT embed link
											$replace = array( 'watch?v=', '&feature=youtu.be' );
											$new = array( 'embed/', '' );
											$youtube_url = str_ireplace ( $replace , $new , $link );
											var_dump($youtube_url);
										    echo '<iframe width="560" height="315" src="' . $youtube_url . '" frameborder="0" allowfullscreen></iframe>';
										} else {
											echo $link;
										}

						            }

						            else {
						                if( $picture_url ) {
						                    echo "<div class='post-picture'>";
						                        echo "<img src='{$picture_url}' width='250' alt='Post picture'/>";
						                    echo "</div>";
						                }

						                echo "<div class='post-info'>";
						                    echo "<div class='post-info-name'>{$name}</div>";
						                    echo "<div class='post-info-description'>{$description}</div>";
						                echo "</div>";
						            }

						        echo "</div>";
						    //echo "</a>";
						echo "</div>";

					echo "</div>";
				}


		echo $args['after_widget'];
	}

	// Widget Backend
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'pemp_widget_domain' );
		}

		if ( isset( $instance[ 'no_of_posts' ] ) ) {
			$no_of_posts = $instance[ 'no_of_posts' ];
		}
		else {
			$no_of_posts = 3; //Default
		}

		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'no_of_posts' ); ?>"><?php _e( 'Number of posts:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'no_of_posts' ); ?>" name="<?php echo $this->get_field_name( 'no_of_posts' ); ?>" type="number" value="<?php echo esc_attr( $no_of_posts ); ?>" min="1" max="20" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		$instance['no_of_posts'] = ( ! empty( $new_instance['no_of_posts'] ) ) ? strip_tags( $new_instance['no_of_posts'] ) : '';

		return $instance;
	}

} // Class pemp_widget ends here

// Register and load the widget
function pemp_load_widget() {
	register_widget( 'pemp_widget' );
}
add_action( 'widgets_init', 'pemp_load_widget' );


// to get 'time ago' text
function time_elapsed_string($datetime, $full = false) {

    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}