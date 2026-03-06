<?php

/*----------------------------------------------------------
  メインカテゴリー（Yoast）取得
----------------------------------------------------------*/

function get_main_category($ID, $taxonomy_name = 'category' ){
  if(!get_the_terms( $ID, $taxonomy_name )){
    return false;

  } elseif( count(get_the_terms( $ID, $taxonomy_name )) == 1 ){
    return get_the_terms( $ID, $taxonomy_name )[0];

  } elseif ( count(get_the_terms( $ID, $taxonomy_name )) != 0 ){
    if (class_exists('WPSEO_Primary_Term')) {
      $wpseo_primary_term = new WPSEO_Primary_Term($taxonomy_name, $ID);
      if($wpseo_primary_term->get_primary_term()){
        $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
        $term = get_term($wpseo_primary_term);
        return $term;
      }else{
        // 一括編集の場合はメインカテゴリーが設定されないので対処
        return get_the_terms( $ID, $taxonomy_name )[0];
      }
    }
  }else{
    return false;
  }
}



/*----------------------------------------------------------
  archiveの2ページ目以降をnoindex
----------------------------------------------------------*/

function custom_robots($link) {
  if (is_paged()){
    return "noindex,follow";
  }else{
    return $link;
  }
}
add_filter( 'wpseo_robots', 'custom_robots' );

?>