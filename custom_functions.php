<?php

// 今回は「投稿」を使用していないので非表示にする
function remove_menus()
{
  global $menu;
  remove_menu_page('edit.php');
}
add_action('admin_menu', 'remove_menus');

// taxonomyのdescription取得
function get_tax_description($oem_id)
{
  $description = term_description($oem_id, 'description');
  if (!$description) {
    $oem_ref_id = get_field('base_data', "report_name_{$oem_id}");
    $description = term_description($oem_ref_id, 'description');
  }
  return $description;
}

// CTAX: フィールドが空欄の場合はrefを参照する
function get_ref_tax_field($field)
{
  global $oem_id;
  $oem_data = get_field($field, "report_name_{$oem_id}");
  if (!$oem_data) {
    $oem_ref_id = get_field('base_data', "report_name_{$oem_id}");
    $oem_data = get_field($field, "report_name_{$oem_ref_id}");
  }
  return $oem_data;
}

// CPT: フィールドが空欄の場合はrefを参照する
function get_report_field($field)
{
  global $post;
  $data = get_field($field, $post->id);
  if (!$data) {
    $ref_id = get_field('reference_id', $post->id);
    $data = get_field($field, $ref_id);
  }
  return $data;
}

// CPT: 損益率のラベル取得
function get_label($key)
{
  switch ($key) {
    case 'month_1':
      $result = '1ヶ月';
      break;
    case 'month_6':
      $result = '6ヶ月';
      break;
    case 'month_12':
      $result = '12ヶ月';
      break;
    case 'month_36':
      $result = '36ヶ月';
      break;
    case 'origin':
      $result = '設定来';
      break;
  }
  return $result;
}

/*----------------------------------------------------------
  CoinGecko API プロキシ（CORS回避）
----------------------------------------------------------*/
add_action('wp_ajax_coingecko_proxy',        'coingecko_proxy_handler');
add_action('wp_ajax_nopriv_coingecko_proxy', 'coingecko_proxy_handler');

function coingecko_proxy_handler()
{
  $coin_id = isset($_GET['coin_id']) ? sanitize_text_field($_GET['coin_id']) : '';
  $vs      = isset($_GET['vs'])      ? sanitize_text_field($_GET['vs'])      : 'usd';
  $days    = isset($_GET['days'])    ? intval($_GET['days'])                 : 365;

  if (empty($coin_id)) {
    wp_send_json_error('coin_id is required', 400);
  }

  // 許可するcoin_idをホワイトリストで制限
  $allowed_coins = ['bitcoin', 'ethereum'];
  if (!in_array($coin_id, $allowed_coins, true)) {
    wp_send_json_error('coin_id not allowed', 403);
  }

  // トランジェントキャッシュ（1時間）
  $cache_key = "cg_market_{$coin_id}_{$vs}_{$days}";
  $cached    = get_transient($cache_key);
  if ($cached !== false) {
    wp_send_json($cached);
  }

  $api_url = add_query_arg([
    'vs_currency' => $vs,
    'days'        => $days,
  ], "https://api.coingecko.com/api/v3/coins/{$coin_id}/market_chart");

  $response = wp_remote_get($api_url, [
    'timeout' => 15,
    'headers' => [],
  ]);

  if (is_wp_error($response)) {
    wp_send_json_error($response->get_error_message(), 502);
  }

  $code = wp_remote_retrieve_response_code($response);
  $body = wp_remote_retrieve_body($response);

  if ($code !== 200) {
    wp_send_json_error("CoinGecko API returned {$code}", $code);
  }

  $data = json_decode($body, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    wp_send_json_error('Invalid JSON from CoinGecko', 502);
  }

  set_transient($cache_key, $data, HOUR_IN_SECONDS);
  wp_send_json($data);
}

// フロント用に admin-ajax.php の URL を出力
add_action('wp_enqueue_scripts', function () {
  wp_localize_script('bundle_js', 'wpApiSettings', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
  ]);
});
