<?php
/*
Plugin Name: BNS Featured Tag
Plugin URI: http://buynowshop.com/plugins/bns-featured-tag/
Description: Plugin with multi-widget functionality that displays most recent posts from specific tag or tags (set with user options). Also includes user options to display: Tag Description; Author and meta details; comment totals; post categories; post tags; and either full post or excerpt (or any combination).
Version: 1.6.2.2
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
*/

/*
**
* Copyright 2009, 2010 Edward Caissie 
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
* 
* Plugin Changelog: see readme.txt
**
*/

global $wp_version;
$exit_message = 'BNS Featured Tag requires WordPress version 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>';
if (version_compare($wp_version, "2.8", "<")) {
	exit ($exit_message);
}

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'load_bns_featured_tag_widget' );

/* Function that registers our widget. */
function load_bns_featured_tag_widget() {
	register_widget( 'BNS_Featured_Tag_Widget' );
}

// Begin the mess of Excerpt Length fiascoes
function get_first_words_for_bns_ft($text, $length = 55) {
	if (!$length)
		return $text;
		
	$text = strip_tags($text);
	$words = explode(' ', $text, $length + 1);
	if (count($words) > $length) {
		array_pop($words);
		array_push($words, '...');
		$text = implode(' ', $words);
	}
	return $text;
}
// End Excerpt Length

class BNS_Featured_Tag_Widget extends WP_Widget {

	function BNS_Featured_Tag_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bns-featured-tag', 'description' => __('Displays most recent posts from a specific featured tag or tags.') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 450, 'height' => 350, 'id_base' => 'bns-featured-tag' );

		/* Create the widget. */
		$this->WP_Widget( 'bns-featured-tag', 'BNS Featured Tag', $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title          = apply_filters('widget_title', $instance['title'] );
		$tag_choice     = $instance['tag_choice'];
		$show_count     = $instance['show_count'];
		$show_tag_desc  = $instance['show_tag_desc'];
		$show_meta      = $instance['show_meta'];
		$show_comments  = $instance['show_comments'];
		$show_cats		  = $instance['show_cats'];
		$show_tags		  = $instance['show_tags'];
		$only_titles    = $instance['only_titles'];
		$show_full		  = $instance['show_full'];
		$excerpt_length = $instance['excerpt_length'];
		$count          = $instance['count']; /* Plugin requires counter variable to be part of its arguments?! */
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display posts from widget settings. */
		query_posts("tag=$tag_choice&posts_per_page=$show_count");
		if ( $show_tag_desc ) {
		  echo '<div class="bnsfc-tag-desc">' . tag_description() . '</div>';
		}
		if (have_posts()) : while (have_posts()) : the_post();
		/* static $count = 0; */ /* see above */
		
			if ($count == $show_count) {
				break;
			} else { ?>
				<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<strong><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></strong>
					<div class="post-details">
						<?php if ( $show_meta ) {  
							_e('by '); the_author(); _e(' on '); the_time('M j, Y'); ?><br />
						<?php }
						if ( $show_comments ) {         
							_e('with '); comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '',__('Comments Closed')); ?><br />
						<?php } 
						if ( $show_cats ) { 
							_e('in '); the_category(', '); ?><br />
						<?php } 
						if ( $show_tags ) { 
							the_tags(__('as '), ', ', ''); ?><br />
						<?php } ?>
					</div> <!-- .post-details -->
					<?php if ( !$only_titles ) { ?>
						<div style="overflow-x: auto"> <!-- for images wider than widget area -->
							<?php if ( $show_full ) { 
								the_content();
							} else if (isset($instance['excerpt_length']) && $instance['excerpt_length'] > 0) {
								echo get_first_words_for_bns_ft(get_the_content(), $instance['excerpt_length']);
							} else {
								the_excerpt();
							} ?>
						</div>
					<?php } ?>
				</div> <!-- .post #post-ID -->
				<?php $count++; }
		
			endwhile;
		else : 
			_e('Yes, we have no bananas, or posts, today.');
		endif; 
	  
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['tag_choice']	    = strip_tags( $new_instance['tag_choice'] );
		$instance['show_count']     = strip_tags( $new_instance['show_count'] );
		$instance['show_tag_desc']  = $new_instance['show_tag_desc'];
		$instance['show_meta']		  = $new_instance['show_meta'];
		$instance['show_comments']	= $new_instance['show_comments'];
		$instance['show_cats']		  = $new_instance['show_cats'];
		$instance['show_tags']		  = $new_instance['show_tags'];
		$instance['only_titles']    = $new_instance['only_titles'];
		$instance['show_full']		  = $new_instance['show_full'];
		$instance['excerpt_length'] = $new_instance['excerpt_length'];
		$instance['count']          = $new_instance['count']; /* added to be able to reset count to zero for every instance of the plugin */
		
		return $instance;
	}

	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array(
				'title'           => __('Featured Tag'),
				'tag_choice'		  => '',
				'count'           => '0', /* resets count to zero as default */
				'show_count'		  => '3',
				'show_tag_desc'   => false,
				'show_meta'			  => false,
				'show_comments'		=> false,
				'show_cats'			  => false,
				'show_tags'			  => false,
				'only_titles'     => false,
				'show_full'			  => false,
				'excerpt_length'  => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'tag_choice' ); ?>"><?php _e('Tag Names, separated by commas:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'tag_choice' ); ?>" name="<?php echo $this->get_field_name( 'tag_choice' ); ?>" value="<?php echo $instance['tag_choice']; ?>" style="width:100%;" />
		</p>
		
  	<p>
				<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_tag_desc'], true ); ?> id="<?php echo $this->get_field_id( 'show_tag_desc' ); ?>" name="<?php echo $this->get_field_name( 'show_tag_desc' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_tag_desc' ); ?>"><?php _e('Show first Tag choice description?'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e('Total Posts to Display:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="<?php echo $instance['show_count']; ?>" style="width:100%;" />
		</p>

		<table width="100%">
			<tr>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_meta'], true ); ?> id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_meta' ); ?>"><?php _e('Display Author Meta Details?'); ?></label>
					</p>
				</td>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_comments'], true ); ?> id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>"><?php _e('Display Comment Totals?'); ?></label>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_cats'], true ); ?> id="<?php echo $this->get_field_id( 'show_cats' ); ?>" name="<?php echo $this->get_field_name( 'show_cats' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_cats' ); ?>"><?php _e('Display the Post Categories?'); ?></label>
					</p>
				</td>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_tags'], true ); ?> id="<?php echo $this->get_field_id( 'show_tags' ); ?>" name="<?php echo $this->get_field_name( 'show_tags' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_tags' ); ?>"><?php _e('Display the Post Tags?'); ?></label>
					</p>
				</td>
			</tr>
		</table>
		
		<hr /> <!-- separates meta details display from content/excerpt display options -->
		<p>The default is to show the excerpt, if it exists, or the first 55 words of the post as the excerpt.</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['only_titles'], true ); ?> id="<?php echo $this->get_field_id( 'only_titles' ); ?>" name="<?php echo $this->get_field_name( 'only_titles' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e('Display only the Post Titles?'); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_full'], true ); ?> id="<?php echo $this->get_field_id( 'show_full' ); ?>" name="<?php echo $this->get_field_name( 'show_full' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e('Display entire Post? (defaults to Post excerpt)'); ?></label>
		</p>
		
		<p>
  		<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e('Set your preferred value for the amount of words'); ?></label>
  		<input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" style="width:100%;" />
  	</p>
		<?php
	}
}
?>