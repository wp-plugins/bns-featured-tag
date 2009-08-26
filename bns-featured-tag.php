<?php
/*
Plugin Name: BNS Featured Tag
Plugin URI: http://buynowshop.com/plugins/bns-featured-tag/
Description: Plugin with multi-widget functionality that displays most recent posts from specific tag or tags (set with user options). Also includes user options to display: Author and meta details; comment totals; post categories; post tags; and either full post or excerpt (or any combination).  
Version: 1.0
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
*/

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'load_bns_featured_tag_widget' );

/* Function that registers our widget. */
function load_bns_featured_tag_widget() {
	register_widget( 'BNS_Featured_Tag_Widget' );
}

class BNS_Featured_Tag_Widget extends WP_Widget {

function BNS_Featured_Tag_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bns-featured-tag', 'description' => __('Displays most recent posts from a specific featured tag or tags.') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'bns-featured-tag' );

		/* Create the widget. */
		$this->WP_Widget( 'bns-featured-tag', 'BNS Featured Tag', $widget_ops, $control_ops );
	}
	
function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$tag_choice = $instance['tag_choice'];
		$show_count = $instance['show_count'];
		$show_meta = $instance['show_meta'];
		$show_comments = $instance['show_comments'];
		$show_cats = $instance['show_cats'];
		$show_tags = $instance['show_tags'];
		$show_full = $instance['show_full'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display posts from widget settings. */
    query_posts("tag=$tag_choice");
    echo $tag_choice;
    if (have_posts()) : while (have_posts()) : the_post();
      static $count = 0;
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
        <?php if ( $show_full ) { 
          the_content(__('Read more... '));
        } else {
          the_excerpt(); 
        } ?>
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
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['tag_choice'] = strip_tags( $new_instance['tag_choice'] );
		$instance['show_count'] = strip_tags( $new_instance['show_count'] );
		$instance['show_meta'] = strip_tags( $new_instance['show_meta'] );
		$instance['show_comments'] = strip_tags( $new_instance['show_comments'] );
		$instance['show_cats'] = strip_tags( $new_instance['show_cats'] );
		$instance['show_tags'] = strip_tags( $new_instance['show_tags'] );
		$instance['show_full'] = strip_tags( $new_instance['show_full'] );
		
		return $instance;
	}

function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array(
      'title' => __('Featured Tag'),
      'tag_choice' => '',
      'show_count' => '3',
      'show_meta' => false,
      'show_comments' => false,
      'show_cats' => false,
      'show_tags' => false,
      'show_full' => false
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
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e('Total Posts to Display:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="<?php echo $instance['show_count']; ?>" style="width:100%;" />
		</p>

    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_meta'], true ); ?> id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_meta' ); ?>"><?php _e('Display Author Meta Details?'); ?></label>
		</p>

    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_comments'], true ); ?> id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" />
      <label for="<?php echo $this->get_field_id( 'show_comments' ); ?>"><?php _e('Display Comment Totals?'); ?></label>
		</p>

    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_cats'], true ); ?> id="<?php echo $this->get_field_id( 'show_cats' ); ?>" name="<?php echo $this->get_field_name( 'show_cats' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_cats' ); ?>"><?php _e('Display the Post Categories?'); ?></label>
		</p>

    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_tags'], true ); ?> id="<?php echo $this->get_field_id( 'show_tags' ); ?>" name="<?php echo $this->get_field_name( 'show_tags' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_tags' ); ?>"><?php _e('Display the Post Tags?'); ?></label>
		</p>

    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_full'], true ); ?> id="<?php echo $this->get_field_id( 'show_full' ); ?>" name="<?php echo $this->get_field_name( 'show_full' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e('Display entire Post? (defaults to Post excerpt)'); ?></label>
		</p>


	<?php
	}
}
?>
