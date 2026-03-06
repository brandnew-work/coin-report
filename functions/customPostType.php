<?php

/*----------------------------------------------------------
  カスタム投稿の追加
----------------------------------------------------------*/

add_action('init', 'create_post_type');
function create_post_type()
{
  register_post_type('report', [
    'labels' => [
      'name' => 'レポート',
      'singular_name' => 'report',
    ],
    'public' => true,
    'hierarchical' => true,
    'has_archive' => false,
    'menu_position' => 6,
    'show_in_rest' => true,
    'taxonomies' => ['report_name'],
    'supports' => [
      'title',
      'revisions',
    ],
  ]);
  register_taxonomy(
    'report_name',
    ['report'],
    [
      'rewrite' => ['slug' => '/report'],
      'label' => 'ex_name',
      'hierarchical' => true,
      'show_admin_column' => true,
      'show_ui' => true,
      'query_var' => true,
      'show_in_rest' => false,
    ]
  );
}

?>
