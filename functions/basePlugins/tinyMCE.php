<?php

/*----------------------------------------------------------
  TinyMCEのWysiwyg Editorにスタイルを追加
----------------------------------------------------------*/

function plugin_mce_css( $mce_css ) {
  if ( ! empty( $mce_css ) ) $mce_css .= ',';
  $font_url = get_template_directory_uri().'/css/editor_style.css';
  $mce_css .= str_replace( ',', '%2C', $font_url );
  return $mce_css;
}
add_filter( 'mce_css', 'plugin_mce_css' );



/*----------------------------------------------------------
  TinyMCEのWysiwyg Editorスタイルのキャッシュ消去
----------------------------------------------------------*/

function extend_tiny_mce_before_init( $mce_init ) {
  $mce_init['cache_suffix']= 'v='.time();
  return $mce_init;
}
add_filter( 'tiny_mce_before_init', 'extend_tiny_mce_before_init' );



/*----------------------------------------------------------
  TinyMCEに関するjsの読み込み
----------------------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'tableSpScript' );
function tableSpScript() {
	wp_enqueue_script(
		'tableSp_script',
		get_template_directory_uri().'/functions/basePlugins/tinyMCE.js',
		array()
	);
}



/*----------------------------------------------------------
  TinyMCEのエディタ項目変更
----------------------------------------------------------*/

function my_mce4_options( $init ) {

// color settings ------------------------------------------------
$custom_colors = array(
  "ff0000" , "red",
);
$init['textcolor_map']    = json_encode($custom_colors);
$init['textcolor_rows']   = 6;  // 色を最大何行まで表示させるか
$init['textcolor_cols']   = 10; // 色を最大何列まで表示させるか

// font-size setting ------------------------------------------------
$init['fontsize_formats'] = '12px=0.75rem 14px=0.875rem 18px=1.125rem 20px=1.25rem 26px=1.625rem 32px=2.125rem';

// body class setting ------------------------------------------------
$init['body_class'] = 'editor-area';

// style settings ------------------------------------------------
// $style_formats = array(
//   array(
//     'title'   => 'スタイル名',
//     'inline'  => 'span',
//     'classes' => 'marker'
//   ),
// );
// $init['style_formats']    = json_encode($style_formats);

// format settings ------------------------------------------------
$formats = array(
  'bold'   => array('inline' => 'b'),
  'italic' => array('inline' => 'i'),
  'small' => array('inline' => 'small'),
);
$init['formats'] = json_encode( $formats );

// table settings ------------------------------------------------
$table_default_styles = array(
  'table' => 'width height',
  'th'    => 'width height',
  'td'    => 'width height',
);
$init['table_default_styles'] = json_encode($table_default_styles);

// default class
$default_table_class = 'table';

$table_class_list = array(
  array(
    'title' => '初期設定',
    'value' => $default_table_class
  ),
  array( // wrapper設定: tinyMCE_modules.js / css設定: _base.scss
    'title' => 'スクロール',
    'value' => 'table-scroll'
  ),
);
$init['table_class_list'] = json_encode($table_class_list);

// default class setting
$table_default_attributes = array(
  'class' => $default_table_class,
);
$init['table_default_attributes'] = json_encode($table_default_attributes);


// ------------------------------------------------
return $init;
}
add_filter('tiny_mce_before_init', 'my_mce4_options');



/*----------------------------------------------------------
  追加ボタン設定（別途 tinyMCE.jsにて設定必要）
----------------------------------------------------------*/

// 作成したプラグインを登録 ------------------------------------------------
function register_mce_external_plugins( $plugin_array ) {
  $plugin_array[ 'original_tinymce_button_plugin' ] = get_template_directory_uri() . '/functions/basePlugins/tinyMCE_modules.js';
  return $plugin_array;
}
add_filter( 'mce_external_plugins', 'register_mce_external_plugins' );


// プラグインで作ったボタンを登録 ------------------------------------------------
function add_mce_buttons( $buttons ) {
  // $buttons[] = 'box';
  // $buttons[] = 'marker';
  $buttons[] = 'button';
  // $buttons[] = 'br';
  return $buttons;
}
add_filter( 'mce_buttons', 'add_mce_buttons' );


?>