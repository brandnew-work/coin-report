<?php
/* FYI：
    OEMという表記は表に出てほしくないので、report_nameやex_nameを使用している
*/

get_header();

/*
  taxonomy
*/
$broker_object = wp_get_object_terms($post->ID, 'report_name')[0];
$broker_id     = $broker_object->term_id;

// 共通
$broker_common = [
  'logo'         => get_field('logo', "report_name_{$broker_id}") ?? '',
  'name'         => $broker_object->name ?? '',
  'description'  => term_description($broker_id, 'report_name') ?? '',
  'market'       => get_field('market', "report_name_{$broker_id}") ?? '',
  'fee'          => get_field('fee') ?? '',
  'info'         => [
    'サポート時間' => get_field('time_support', "report_name_{$broker_id}") ?? '',
    '公式LINE'    => get_field('line', "report_name_{$broker_id}")['url'] ?? '',
    'URL'        => get_field('url', "report_name_{$broker_id}") ?? '',
  ],
];

// 固有
$user_unique = [
  'details'      => [
    '運用開始日'   => get_field('start_date') ?? '',
    '獲得利益額'   => get_field('total_profit') ?? '',
    'アドレス'     => get_field('address') ?? '',
  ],
];

// foreachで出力するため、空配列を除外
$broker_common['info'] = array_filter($broker_common['info']);
$user_unique['details'] = array_filter($user_unique['details']);
$config = array_merge_recursive($user_unique, $broker_common);

// 利益
$profits = get_field('profits');
$show_profits = array_slice($profits, -6);
if (!empty($profits)) {
  $first_date = explode('/', $profits[0]['date']);
  $start_month = str_pad($first_date[0], 2, '0', STR_PAD_LEFT);
  $edited_start_date = "{$first_date[1]}-{$start_month}-01";
} else {
  $edited_start_date = '';
}

/*
  graph（$profitsベース）
  実質利益のみ累積加算で表示
*/
$graph_labels = [];
$graph_values = [];
if (!empty($profits)) {
  $cumulative = 0;
  foreach ($profits as $data) {
    array_push($graph_labels, $data['date']);
    $cumulative += floatval(str_replace(',', '', $data['net']));
    array_push($graph_values, $cumulative);
  }
}

// 運用状況
$state_tables = [
  '起点日の状況' => ['thead' => ['項目', '詳細'], 'data' => get_field('state_start')],
  '現状の資産残高'   => ['thead' => ['項目', '詳細'], 'data' => get_field('state_balance')],
  'ウォレット状況'   => ['thead' => ['項目', '詳細'], 'data' => get_field('state_wallet')],
  '運用結果まとめ'   => ['thead' => ['項目', '詳細'], 'data' => get_field('state_result'), 'notice' => '※ 途中出金または複利設定をしている場合、【ETH換算枚数が減少】する可能性があります。'],
];
$state_tables = array_filter($state_tables, fn($row) => !empty($row['data']));
?>

<script>
  const labels = <?= json_encode($graph_labels) ?>;
  const values = <?= json_encode($graph_values) ?>;
</script>
<main>
  <article id="page2" class="page2">
    <header class="header">
      <div class="inner">
        <h1 class="title-page">
          <span class="title-page__text">月次運用レポート</span>
        </h1>
      </div>
    </header>
    <div class="ex-head">
      <div class="inner">
        <div class="ex-head__contents">
          <div class="ex-head__logo">
            <img src="<?= $config['logo'] ?>" alt="<?= $config['name'] ?>">
          </div>
          <div class="ex-head__info">
            <p class="ex-head__info-title"><?= $config['name'] ?></p>
            <div class="ex-head__info-meta --flex">
              <?php foreach ($config['details'] as $label => $detail): ?>
                <dl class="ex-head__info-meta__row">
                  <dt><?= $label ?></dt>
                  <dd>
                    <?php if ($label === '獲得利益額'): ?>
                      $<?= $detail ?>　<span class="remark">※ 成功報酬差引き後</span>
                    <?php else: ?>
                      <?= $detail ?>
                    <?php endif; ?>
                  </dd>
                </dl>
              <?php endforeach; ?>
            </div>
            <?php if (!empty($config['description'])): ?>
              <div class="ex-head__info-description">
                <?= $config['description']; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php if ($config['market']): ?>
      <section>
        <div class="inner">
          <?php get_template_part('components/title', '', ['今月の為替市況']) ?>
          <div class="contents">
            <?= $config['market'] ?>
          </div>
        </div>
      </section>
    <?php endif; ?>
    <?php if (!empty($show_profits)): ?>
      <section class="section">
        <div class="inner">
          <?php get_template_part('components/title', '', ['各月の獲得利益額', '※表示桁数未満は四捨五入']) ?>
          <div class="contents">
            <table class="table table-data">
              <tr>
                <th>年月</th>
                <?php foreach ($show_profits as $v): ?>
                  <th><?= $v['date'] ?></th>
                <?php endforeach; ?>
              </tr>
              <tr>
                <td>表面利益</td>
                <?php foreach ($show_profits as $v): ?>
                  <td>$<?= $v['gross'] ?></td>
                <?php endforeach; ?>
              </tr>
              <tr>
                <td>実質利益</td>
                <?php foreach ($show_profits as $v): ?>
                  <td>$<?= $v['net'] ?></td>
                <?php endforeach; ?>
              </tr>
            </table>
          </div>
        </div>
      </section>
    <?php endif; ?>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['獲得利益額の推移', '※「獲得利益額」の数値と「利益額の推移」にわずかなズレが発生する場合がございます ']) ?>
        <div class="contents">
          <canvas class="js-chart performance-chart"></canvas>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner --separate">
        <div class="col">
          <?php get_template_part('components/title', '', ['BTC']); ?>
          <div class="contents">
            <canvas
              class="js-crypto-chart"
              data-coin="bitcoin"
              data-label="BTC (USD)"
              data-days="365"
              data-vs="usd"
              height="300"></canvas>
          </div>
          <?php get_template_part('components/title', '', ['ETH']); ?>
          <div class="contents">
            <canvas
              class="js-crypto-chart"
              data-coin="ethereum"
              data-label="ETH (USD)"
              data-days="365"
              data-vs="usd"
              height="300"></canvas>
          </div>
        </div>
        <div class="col">
          <?php get_template_part('components/title', '', ['運用状況']) ?>
          <div class="contents state-section">
            <?php foreach ($state_tables as $title => $row): ?>
              <div class="state-section__item">
                <p class="state-section__item-title"><?= $title ?></p>
                <?php get_template_part('/components/table-data', '', $row) ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <dl class="oem-info-list">
          <?php foreach ($config['info'] as $label => $info): ?>
            <dt><?= $label ?>:</dt>
            <dd>
              <?= $info ?>
              <?php if ($label === 'サポート時間'): ?>
                <span class="remark">（※3営業日以内にご返答をさせて頂きます）</span>
              <?php endif; ?>
            </dd>
          <?php endforeach; ?>
        </dl>
        <p class="remark">
          当資料は、現在運用に参加している投資家様へお知らせをするための資料となります。<br>
          契約の募集および投資勧誘の目的としたものではありません。また予告なしに当資料の内容が変更、廃止される場合がありますので予めご了承下さいませ。
        </p>
      </div>
    </section>
  </article>
  <div class="divider"></div>
  <article id="page1" class="page1">
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['このプロジェクトについて']) ?>
        <div class="contents">
          <div class="vmv-section">
            <div class="vmv-item --vision">
              <p class="vmv-item__title" en="vision">プロジェクト理念</p>
              <div class="vmv-item__contents">
                暗号資産の資産形成を教育と検証で「<b>標準化</b>」する
              </div>
            </div>
            <div class="vmv-item --mission">
              <p class="vmv-item__title" en="mission">ミッション</p>
              <div class="vmv-item__contents">
                個人が理解し、選び、継続できるように教育と実績に基づく運用商品を提供する
              </div>
            </div>
            <div class="vmv-item --value">
              <p class="vmv-item__title" en="value">バリュー</p>
              <div class="vmv-item__contents">
                <dl class="value-column">
                  <dt>教育</dt>
                  <dd>誰でも判断できる知識に落とす</dd>
                </dl>
                <dl class="value-column">
                  <dt>検証</dt>
                  <dd>実績を再現性として磨き続ける</dd>
                </dl>
                <dl class="value-column">
                  <dt>誠実</dt>
                  <dd>リスクも含めて伝える</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['この資料について']) ?>
        <div class="contents">
          本文書は、ユーザーがUniswapの分散型プロトコルを利用して、暗号資産の流動性提供等を行うにあたり、重要事項（運用結果・同意事項・リスク・免責）を説明するものです。<br>
          当社（または運営者）は、特定のプロトコルの開発者・運営者・管理者ではなく、ユーザーは自己責任で外部プロトコルを利用します。<br>
          本サービス/情報提供は、投資助言・勧誘・売買推奨ではありません。
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['運用実績・状況について']) ?>
        <div class="contents">
          運用内容や状況などは信頼できると考えられる情報に基づき作成をしておりますが、その正確性・完全性については保証するものではありません。「運用実績・状況」に係る内容はいかなるものも過去の実績であり、将来の運用成果を示唆あるいは保証するものではありません。
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['免責事項']) ?>
        <div class="contents">
          <ul class="list-dot">
            <li>当社は、以下について明示・黙示を問わず一切保証しません。
              <ul>
                <li>利益の発生、損失の回避、元本の維持、価格・APRの安定、スリッページの最小化</li>
                <li>プロトコルの安全性、継続稼働、第三者サービス（RPC、ブリッジ、ウォレット等）の可用性</li>
              </ul>
            </li>
            <li>ユーザーが被った損害（直接損害・間接損害・逸失利益・機会損失・データ損失等）について、当社は責任を負えません。</li>
            <li>ネットワーク混雑やガス代高騰、MEV等により、想定外の取引コストが発生し得ます。</li>
          </ul>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['同意事項']) ?>
        <div class="contents">
          <ul class="list-dot">
            <li>秘密鍵/シードフレーズは自己管理であり、当社は復旧できない</li>
            <li>取引は原則として取り消し不可で、誤送金・誤操作の救済が困難</li>
            <li>Uniswap等の利用は第三者（分散型プロトコル）との取引であり、当社は当該プロトコルを管理・運営していない</li>
            <li>当社の提示する情報（APR等）は推定/過去データ/前提条件に基づく場合があり、将来結果を保証しない</li>
            <li>自国/居住地の法令に照らし、暗号資産取引・DeFi利用が適法であることを確認している</li>
            <li>反社・制裁対象・マネロン等に該当しない（必要なら制裁リスト等も）</li>
            <li>税務申告を含む法令上の義務を自己の責任で履行する</li>
          </ul>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['投資リスク']) ?>
        <div class="contents">
          <ul class="list-num">
            <li>価格変動リスク<br>
              暗号資産は価格変動が大きく、短時間で大幅な下落が起こり得ます。</li>
            <li>インパーマネントロス（IL）リスク
              <ul>
                <li>価格が変動すると、単純保有（HODL）よりも受取資産の構成が変化し、結果として相対的損失（IL）が発生し得ます。</li>
                <li>手数料収入がILを上回る保証はありません。</li>
              </ul>
            </li>
            <li>流動性レンジ/乖離リスク（Uniswap v3系）
              <ul>
                <li>レンジ外になると、片側資産に偏り、手数料が発生しにくくなるまたはゼロになる可能性があります。</li>
                <li>リバランスや再配置にはガス代等のコストがかかります。</li>
              </ul>
            </li>
            <li>スマートコントラクト/プロトコルリスク<br>
              バグ、脆弱性、仕様変更、ガバナンス攻撃等により、資産の喪失・凍結・引出不能が生じ得ます。<br>
              監査の有無は安全性を保証しません。
            </li>
            <li>ブリッジ/クロスチェーンリスク（Base等を含む）<br>
              ブリッジは過去に多数の重大事故があり、ハッキング・停止・巻き戻り等により損失が生じ得ます。
            </li>
            <li>ステーブルコインのデペグ/償還リスク<br>
              USDC/USDT等も、完全な価格安定や償還を保証するものではなく、デペグや規制・凍結等の影響があり得ます。
            </li>
            <li>MEV（サンドイッチ等）・オラクル・価格操作リスク
              <ul>
                <li>取引がMEVの対象となり不利約定や追加コストが生じる可能性があります。</li>
                <li>流動性が薄い銘柄は価格操作の影響を受けやすい。</li>
              </ul>
            </li>
            <li>カウンターパーティ/権限（Admin Key）リスク（対象がある場合）<br>
              対象トークンや周辺プロトコルに管理者権限があると、凍結・徴収・仕様変更等が行われる可能性があります。
            </li>
            <li>規制・税務・会計リスク
              <ul>
                <li>法令解釈や規制が変更される可能性があります。税務上の取扱いも変更され得ます。</li>
                <li>申告漏れや計算誤りの責任はユーザーにあります。</li>
              </ul>
            </li>
            <li>システム/運用リスク
              <ul>
                <li>RPC障害、ウォレット障害、UIの不具合、ネットワーク障害により、取引遅延・失敗が起こり得ます。</li>
                <li>フィッシング/偽サイト/偽トークン等の詐欺リスクがあります。</li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['諸費用について']) ?>
        <div class="contents">
          <table class="table">
            <tr>
              <th>項目</th>
              <th>内容</th>
            </tr>
            <tr>
              <td>初期費用（入会）</td>
              <td>無料</td>
            </tr>
            <tr>
              <td>入金手数料</td>
              <td>無料（ETHによるガス代金のみ）</td>
            </tr>
            <tr>
              <td>年間管理費</td>
              <td>無料</td>
            </tr>
            <tr>
              <td>運用関係費（成功報酬）</td>
              <td>（各月）月末時点の発生利益額より<?= $config['fee']; ?></td>
            </tr>
            <tr>
              <td>出金手数料（運用中）</td>
              <td>含み損額の決済が必要となる場合あり</td>
            </tr>
            <tr>
              <td>解約手数料（退会）</td>
              <td>無料</td>
            </tr>
            <tr>
              <td>出金手数料</td>
              <td>無料（ETHによるガス代金のみ）</td>
            </tr>
          </table>
          <p class="remark">
            ※ 運用関係費について<br>
            管理権限機能をご利用していない場合は、毎月15日までに指定アドレス宛へ報酬分の送金を願います。<br>
            送金の確認が取れない場合は、COINPOOLの利用制限を掛けてさせて頂く場合が御座います。
          </p>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="inner">
        <?php get_template_part('components/title', '', ['その他']) ?>
        <div class="contents">
          <ul class="list-dot">
            <li>情報提供の位置づけ<br>
              当社の提供する分析・数値・コメントは教育目的/一般情報で、特定の投資成果を目的とする助言ではない。
            </li>
            <li>禁止事項<br>
              不正アクセス、脆弱性悪用、マネロン、制裁回避、他者の権利侵害、虚偽申告 等。
            </li>
            <li>途中解約/手数料/費用<br>
              ガス代、スリッページ、ブリッジ手数料など、一般的にDEXを利用する際に関わる費用が発生します。
            </li>
            <li>変更条項<br>
              本規約は必要に応じ改定し、改定後に利用した場合は同意したものとみなす。
            </li>
          </ul>
        </div>
      </div>
    </section>
  </article>
</main>

<?php get_footer(); ?>