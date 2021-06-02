if ( $pageTags = get_the_terms( get_the_ID(), 'post_tag' ) ) {
  $pageTagNames = wp_list_pluck( $pageTags, 'name' );

  if( in_array( 'slider', $pageTagNames ) ){
    wp_enqueue_script( 'pdms-slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array( 'jquery' ), '1.9.0', true );
    wp_enqueue_style( 'pdms-slick-styles', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', '1.9.0', 'all' );
  }
}
