<?php
/*
Plugin Name: Somc Subpages Julio Brana
Plugin URI: #
Description: Lists subpages of the current section
Author: Julio Brana
Version: 1.0
*/ 

/** 
 * Register Widget
 *
 */

add_action( 'widgets_init', function(){
     register_widget( 'Somc_Subpages_Widget' );
});

/**
 * Subpages Widget Class
 *
 * @author       Julio Brana <juliobranha@gmail.com>
 */
class Somc_Subpages_Widget extends WP_Widget {
	
    /**
     * Constructor
     *
     * @return void
     **/
        
        function __construct() {
            
            // It would be nice to have some language files
            if(function_exists('load_plugin_textdomain'))
		load_plugin_textdomain('somc_subpages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
            
            parent::__construct(
                    'somc_subpages_widget', // Base ID
                    __('Subpages Widget', 'somc_subpages'), // Name
                    array( 'description' => __( 'Lists current page subpages', 'somc_subpages' ), ) // Args
            );
	}


    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     * @return void Echoes it's output
     **/
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		global $post;

		// Get subpages
		$args = array(
                    'parent' => $post->ID,
                    'child_of' => $post->ID			
		);
		
		$subpages = get_pages( $args );
		
		// If there are no pages, don't display the widget
		if ( empty( $subpages ) ) 
			return;
			
		echo $before_widget;
		
		// Diplay the title (current page)
		$title = esc_attr( get_the_title ( $post->ID ) );
		echo $before_title . $title . $after_title;
                
                // Print the subpages tree
                echo '<div class = "pages-tree">';
                
                echo '<ul style = "display:block;" ><li>';
                
                echo'<a href = "#" class = "parent expanded" > &nbsp; </a>';
                        
                if (has_post_thumbnail( $post->ID )) echo get_the_post_thumbnail( $post->ID, '', array('class' => "subpage_tn") ).'&nbsp;'; 
                
                //Get truncated title
               
                $trunc_title = substr( $title, 0, 20 );
                if ( 20 < strlen( $title ) ) $trunc_title .= '...';
                
                echo '<a href="'
                    . get_page_link( $post->ID ) . '">' 
                    . esc_attr( $trunc_title ) . '</a>';
                
                echo ' <a href = "#" class = "alpha_order asc" /> &nbsp; </a>';
                        
		$this->display_subpages($post->ID, true);
		
                echo '</li></ul></div>';
		echo $after_widget;			
	}
	
	/**
	 * Build the Subpages
	 *
	 * @param array $subpages, array of post objects
	 * @param array $parents, array of parent IDs
	 * @param bool $deep_subpages, whether to include current page's subpages
	 * @return string $output
	 */
	function display_subpages( $page_id, $first_ul ) {
			
		global $post;
                
                // 1st level ul display, others hide
                if( $first_ul ) {
                    $display = 'block';
                } else {
                    $display = 'none';  
                }
		
                // Build the page listing	
		echo '<ul style = " display:' . $display . ';" >';
                
                //Get children + one level depth
                $args = array(                                  
                    'child_of' => $page_id,
                    'parent' => $page_id,
                    'sort_column' => 'post_title',
                );
                $subpages = get_pages( $args );
                
		foreach ( $subpages as $subpage ) {
			$class = array();
                        
                        // Get subpages
                        $args = array(
                            'parent' => $subpage->ID ,
                            'child_of' => $subpage->ID 			
                        );
                        $children = get_pages ( $args );
		        
                        $has_children = !empty( $children );

			echo '<li>';
                        
                        if ( $has_children ) echo'<a href = "#" class = "parent collapsed" > &nbsp; </a>';
                        
                        //featured image
                        if (has_post_thumbnail( $subpage->ID )) echo get_the_post_thumbnail( $subpage->ID, '', array('class' => "subpage_tn") ).'&nbsp;'; 
                        
                        //Get truncated title
                        $thetitle = $subpage->post_title; 
                        $trunc_title = substr( $thetitle, 0, 20 );
                        if ( 20 < strlen( $thetitle ) ) $trunc_title .= '...';
                        
                        echo '<a href="'
                            . get_page_link( $subpage->ID ) . '">' 
                            . esc_attr( $trunc_title ) . '</a>';
			
                        if ( $has_children ) echo ' <a href = "#" class = "alpha_order asc"> &nbsp; </a>';
                     
                        if ( !$has_children ) {   
                            echo '</li>'; // close li
                        } else {
                            $this->display_subpages( $subpage->ID, false ); // loop children
                        }
		}
		echo '</ul>';
	}

	/**
	 * Sanitizes form inputs on save
	 * 
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $new_instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Update widget configuration here */
		
		return $instance;
	}

	/**
	 * Build the widget's form
	 *
	 * @param array $instance, An array of settings for this widget instance 
	 * @return null
	 */
	function form( $instance ) {

		/**
                 * Here we can add the markup for widget options. Some ideas:
                 *  
                 * Depth of subpages shown, 
                 * Display parent title or not, 
                 * Display siblings if not subpages, 
                 * Widget should be displayed if not subages
                 */
		
	}	
}

//shortcode handler
function shortcode_subpages () {
    
    $subpages = new Somc_Subpages_Widget;
    $subpages -> widget (array(                                  
                    'before_widget' => '',
                    'after_widget' => '',
                    'before_title' => '',
                    'after_title' => ''
                ),'');
    return;
	
}

//adding shortcode

add_shortcode( 'somc_subpages_juliobranha', 'shortcode_subpages' );

// Enqueueing styles and scripts 

function somc_styles(){
    wp_register_style('somc_stylesheet', plugins_url('css/style.css', __FILE__));  
    wp_enqueue_style('somc_stylesheet');  
}

add_action('wp_enqueue_scripts', 'somc_styles');

function somc_scripts() {
    wp_register_script('somc_js', plugins_url('javascript/somc.js', __FILE__), array('jquery'),'1.1', true);
    wp_enqueue_script('somc_js');
}

add_action('wp_enqueue_scripts', 'somc_scripts');  

//removing an unwanted class from post_thumbnail 
//TODO fix this issue with CSS instead
remove_action( 'begin_fetch_post_thumbnail_html', '_wp_post_thumbnail_class_filter_add' );
