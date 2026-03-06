<?php


/*------------------------------------------------------
  CSS / js の読み込み
------------------------------------------------------*/

if ( !is_admin() ) {
  define("TEMPLATE_DIRE", get_template_directory_uri());
  define("TEMPLATE_PATH", get_template_directory());
  function wp_css($css_name, $file_path){
    if(file_exists(TEMPLATE_PATH.$file_path)){
      wp_enqueue_style($css_name,TEMPLATE_DIRE.$file_path, array(), date('YmdGis', filemtime(TEMPLATE_PATH.$file_path)));
    }
  }
  function wp_script($script_name, $file_path, $bool = true){
    if(file_exists(TEMPLATE_PATH.$file_path)){
      wp_enqueue_script($script_name,TEMPLATE_DIRE.$file_path, array('jquery'), date('YmdGis', filemtime(TEMPLATE_PATH.$file_path)), $bool);
    }
  }
  function add_scripts($post){
    wp_css('normalize_style','/css/normalize.css');
    wp_css('add_style','/style.css');
    wp_css('add_common_style','/css/common.css');
    wp_script('bundle_js','/js/bundle.js', true);
  }
  add_action( 'wp_enqueue_scripts', 'add_scripts' );

  //scriptにasyncを追加
  function add_async_to_enqueue_script( $url ) {
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.min' ) ) return $url;
    return "$url' async charset='UTF-8";
  }
  add_filter( 'clean_url', 'add_async_to_enqueue_script', 11, 1 );
}


/*------------------------------------------------------
  headerの不要タグ削除
------------------------------------------------------*/

remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


/*------------------------------------------------------
  スマホ表示分岐
------------------------------------------------------*/

function is_mobile(){
  $useragents = array(
    'iPhone', // iPhone
    'iPod', // iPod touch
    'Android.*Mobile', // 1.5+ Android *** Only mobile
    'Windows.*Phone', // *** Windows Phone
    'dream', // Pre 1.5 Android
    'CUPCAKE', // 1.5+ Android
    'blackberry9500', // Storm
    'blackberry9530', // Storm
    'blackberry9520', // Storm v2
    'blackberry9550', // Storm v2
    'blackberry9800', // Torch
    'webOS', // Palm Pre Experimental
    'incognito', // Other iPhone browser
    'webmate' // Other iPhone browser
  );
  $pattern = '/'.implode('|', $useragents).'/i';
  return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}


/*------------------------------------------------------
  Wysiwyg Editorへのスタイル反映
------------------------------------------------------*/
function add_wysiwyg_editor_style() {
  add_editor_style(
    array(
      'css/normalize.css',
      'css/editor-style.css'
    ));
}
add_action('admin_init', 'add_wysiwyg_editor_style');


/*------------------------------------------------------
  外観 -> メニューを使用できるようにする
------------------------------------------------------*/

register_nav_menus();


/*------------------------------------------------------
  アイキャッチ画像を使用できるようにする
------------------------------------------------------*/

add_theme_support('post-thumbnails');


/*------------------------------------------------------
  width height を削除する
------------------------------------------------------*/

add_filter( 'post_thumbnail_html', 'custom_attribute' );
function custom_attribute( $html ){
  $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
  return $html;
}


/*------------------------------------------------------
  固定ページのスラッグをクラス名に追加
------------------------------------------------------*/

function my_body_class($classes) {
  if (is_page()) {
      $page = get_post();
      $classes[] = 'page-'.$page->post_name;
  }
  return $classes;
}
add_filter('body_class', 'my_body_class');


/*------------------------------------------------------
  特定ページのコンテンツを取得（主にフォームにつけるprivacypolicy用）
------------------------------------------------------*/

function get_content_shortcode($atts) {
  extract(shortcode_atts(array(
      'slug' => '',
  ), $atts));
  $page_data = get_page_by_path($slug);
  $page      = get_post($page_data);
  $html      =
<<<EOF
  <div class='content-shortcode-output-{$slug}'>
    <div class='content-shortcode-output-{$slug}__inner'>
      {$page->post_content}
    </div>
  </div>
EOF;
  return $html;
}
add_shortcode('content_shortcode', 'get_content_shortcode');
// wpcf7_add_shortcode('content_shortcode', 'get_content_shortcode'); // CF7につける場合は必要 / MWFは必要なし


/*------------------------------------------------------
  block libraryの初期css消去
------------------------------------------------------*/

function mytheme_enqueue() {
  wp_dequeue_style( 'wp-block-library' );
}
// add_action( 'wp_enqueue_scripts', 'mytheme_enqueue' );


/*------------------------------------------------------
  uploadsフォルダのURL取得
------------------------------------------------------*/

function uploads_url($image_name = ''){
  $upload_dir = wp_upload_dir();
  return "{$upload_dir['baseurl']}/{$image_name}";
}// pictureを毎回書くのがめんどうなので


/*------------------------------------------------------
  ファイル名からretina対応のsrcset取得（retinaサイズの画像登録は別で必要）
------------------------------------------------------*/

// オーバーサイズの自動リサイズ停止
add_filter( 'big_image_size_threshold', '__return_false' );

// retina呼び出し
function srcset($file_name, $size_pc = 'large', $size_sp = 'medium', $alt = '', $img_class = '', $unique_sp = '') {
  $upload_dir = wp_upload_dir();
  $alt_insert = $alt ? " alt='{$alt}'" : '';
  $class_insert = $img_class ? " class='{$img_class}'" : '';
  $file_url   = strpos($file_name, '//') ? $file_name : "{$upload_dir['baseurl']}/{$file_name}";
  $media_id = attachment_url_to_postid( $file_url );
  if($media_id === 0) return ''; // file not found

  $pc_1x = wp_get_attachment_image_src($media_id, $size_pc);
  $pc_2x = wp_get_attachment_image_src($media_id, "{$size_pc}_retina");

  // pc
  $pc = [
    '1x' => $pc_1x[0],
    '2x' => $pc_2x[0],
    'width' => $pc_2x[1],
    'height' => $pc_2x[2],
  ];

  // sp
  if($unique_sp !== '') {
    $file_url   = strpos($file_name, '//') ? $unique_sp : "{$upload_dir['baseurl']}/{$unique_sp}";
    $media_id = attachment_url_to_postid( $file_url );
  }
  $sp_1x = wp_get_attachment_image_src($media_id, $size_sp);
  $sp_2x = wp_get_attachment_image_src($media_id, "{$size_sp}_retina");
  $sp = [
    '1x' => $sp_1x[0],
    '2x' => $sp_2x[0],
  ];

  // html
  $html  = '<picture>';
  $html .=  "<source media='(min-width: 768px)' srcset='{$pc['1x']} 1x, {$pc['2x']} 2x'>";
  $html .=  "<source media='(max-width: 320px)' srcset='{$sp['1x']} 1x, {$sp['2x']} 2x'>";
  $html .= "<img loading='lazy' src='{$sp['1x']}'{$class_insert}{$alt_insert} width='{$pc['width']}' height='{$pc['height']}'>";
  $html .= '</picture>';
  echo $html;
}

/*------------------------------------------------------
  取得系関数
------------------------------------------------------*/

function return_array_field($array, $key, $text = '') {
  if(!is_array($array)) {
    $result = $text == '' ? '' : $text;
  } elseif (array_key_exists($key, $array)) {
    $result = $array[$key] !== '' ? $array[$key] : $text;
  } else {
    $result = $text;
  }
  return $result;
}

// 後でブランクイメージを一括修正したいので
function return_blank( $full = false ) {
  return $full ? uploads_url('blank.svg') : 'blank.svg';
}

// boolの返答
function return_bool($array, $key) {
  $exists = array_key_exists($key, $array);
  $result = $exists ? $array[$key] : true;
  return $result;
}

?>