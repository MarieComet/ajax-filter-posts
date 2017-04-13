<?php
/*
Plugin Name: Ajax Filter Posts
Description: Filter posts by taxonomy with ajax, css based on bootsrap but do what you want.
Version:     1.1
Author:      Marie Comet
*/
class Ajax_Filter_Posts {

	public function __construct(){
		add_action('plugins_loaded', array($this, 'init'), 2);
	}
	public function init(){
		//Add Ajax Actions
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_genre_ajax_scripts' ));
		add_action('wp_ajax_genre_filter', array( $this, 'ajax_genre_filter'));
		add_action('wp_ajax_nopriv_genre_filter', array( $this, 'ajax_genre_filter'));

	}

	//EnqueueScripts

	public function enqueue_genre_ajax_scripts() {
	    wp_register_script( 'genre-ajax-js', plugin_dir_url(__FILE__). 'genre.js', array( 'jquery' ), '', true );
	    wp_localize_script( 'genre-ajax-js', 'ajax_genre_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	    wp_enqueue_script( 'genre-ajax-js' );
		wp_enqueue_style( 'style-css', plugin_dir_url( __FILE__ ) . 'style.css'  );
	}

	/* Public function get_genre_filters accepts two parameters 
	*	$post_type :  the post type slug to query
	* 	$taxo : the taxonomy slug to query
	*	used for display the filters in your template (for example archive.php) like that :
	*	$new_posts_filter = new Ajax_Filter_Posts();
	*	echo $new_posts_filter->get_genre_filters('post', 'category');
	*/
	public function get_genre_filters($post_type, $taxo) {

	    $terms = get_terms($taxo);
	    $filters_html = false;
	 
	    if( $terms ):
	    	$filters_html .= '<div id="genre-filter">';
	    	$filters_html .= '<input type="hidden" name="post_type" value="'. $post_type .'" id="post_type"/>';
	    	$filters_html .= '<input type="hidden" name="taxo" value="'. $taxo .'" id="taxo"/>';
	        foreach( $terms as $term )
	        {
	            $term_id = $term->term_id;
	            $term_name = $term->name;
	 
	            $filters_html .= '<a class="term_id_'.$term_id.' btn btn-large selected"><input type="checkbox" checked="checked" name="filter_genre[]" value="'.$term_id.'" class="input-filter-work">'.$term_name.'</a>';
	        }
	        $filters_html .= '<a class="clear-all btn btn-large selected">Tous</a>';
	        $filters_html .= '</div><div id="genre-results" class="row row-eq-height no-gutters"></div>';
	 
	        return $filters_html;
	    endif;
	}

	/* Public function ajax_genre_filters 
	*	Ajax call construct loop and results
	*/
	public function ajax_genre_filter()
	{
		$query_data = $_GET;

		$post_type = (isset($query_data['post_type'])) ? $query_data['post_type'] : false;
		$taxo = (isset($query_data['taxo'])) ? $query_data['taxo'] : false;

		$genre_terms = (isset($query_data['genres'])) ? explode(',',$query_data['genres']) : false;

		$tax_query = ($genre_terms) ? array( array(
			'taxonomy' => $taxo,
			'field' => 'id',
			'terms' => $genre_terms
		) ) : false;
		
		$search_value = (isset($query_data['search']) ) ? $query_data['search'] : false;
		
		$paged = (isset($query_data['paged']) ) ? intval($query_data['paged']) : 1;
		
		$book_args = array(
			'post_type' => $post_type,
			's' => $search_value,
			'posts_per_page' => 15,
			'tax_query' => $tax_query,
			'paged' => $paged
		);
		$book_loop = new WP_Query($book_args);

		// here place your own loader
		echo '<div class="ajax-post-loader"><img src="' . plugin_dir_url(__FILE__) . '/space-comet-dark.png" class="comet-dark"/></div>';
		
		if( $book_loop->have_posts() ):
			while( $book_loop->have_posts() ): $book_loop->the_post();
			global $post;
			setup_postdata($post);
				get_template_part( 'loop-templates/content', $post_type );
			endwhile; ?>
			<?php
			echo '<div class="genre-filter-navigation col-xs-12 col-sm-12 col-md-12">';
			        $big = 999999999;
			        echo paginate_links( array(
			            'base' => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
			            'format' => '?paged=%#%',
			            'current' => max( 1, $paged ),
			            'total' => $book_loop->max_num_pages
			        ) );

			        echo '</div>';
			?>
		<?php else:
			get_template_part('content-none');
		endif;
		wp_reset_postdata();
		
		die();
	}
}
new Ajax_Filter_Posts();
