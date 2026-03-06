<?php


/*----------------------------------------------------------
  検索結果にカスタムフィールドを含める
----------------------------------------------------------*/

function posts_search_custom_fields($orig_search, $query)
{
	if ($query->is_search() && $query->is_main_query() && !is_admin()) {
		global $wpdb;
		$q = $query->query_vars;
		$n = !empty($q['exact']) ? '' : '%';
		$searchand = $query_temp = '';
		$query_temp = $q['search_terms'];

		if ($query_temp) {
			foreach ($query_temp as $term) {
				$include = '-' !== substr($term, 0, 1);
				if ($include) {
					$like_op = 'LIKE';
					$andor_op = 'OR';
				} else {
					$like_op = 'NOT LIKE';
					$andor_op = 'AND';
					$term = substr($term, 1);
				}
				$like = $n . $wpdb->esc_like($term) . $n;

				$search .= $wpdb->prepare("{$searchand}(($wpdb->posts.post_title $like_op %s) $andor_op ($wpdb->posts.post_content $like_op %s) $andor_op (custom.meta_value $like_op %s))", $like, $like, $like);
				$searchand = ' AND ';
			}
		} // end if

		if (!empty($search)) {
			$search = " AND ({$search}) ";
			if (!is_user_logged_in()) {
				$search .= " AND ($wpdb->posts.post_password = '') ";
			}
		}
		return $search;
	} else {
		return $orig_search;
	}
}


/*----------------------------------------------------------
  ページ固有cssフィールド
----------------------------------------------------------*/

if (function_exists('acf_add_local_field_group')):

	acf_add_local_field_group(array(
		'key' => 'group_620e88d9cd128',
		'title' => 'ページ固有css',
		'fields' => array(
			array(
				'key' => 'field_620e88e3abf40',
				'label' => 'cssファイル名',
				'name' => 'css_file_name',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'ex. top.css',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'page',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'side',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));

endif;



/*----------------------------------------------------------
  map key の 追加
----------------------------------------------------------*/


function my_acf_google_map_api($api)
{
	$api['key'] = 'AIzaSyB3d85-cs8mUU7b4oMQ1LI5Kv7rqcq6UcM';
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
