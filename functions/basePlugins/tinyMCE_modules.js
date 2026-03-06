(function () {

  /*----------------------------------------------------------
    追加ボタン設定
  ----------------------------------------------------------*/

  // box base_code
  function box_html(class_name) {
    var html = "";
    html += "<div class='box_style "+ class_name +"'>";
    html +=   "<div class='box_inner'>";
    html +=     "<div class='box_style_title'>";
    html +=       "<span class='box_style_title_inner'>タイトルが入ります。</span>";
    html +=     "</div>";
    html +=     "ここにコンテンツを記載";
    html +=   "</div>";
    html += "</div>";
    return html;
  }

  // marker base_code
  function marker_html(class_name, text) {
    var html = "";
    html += "<span class='" + class_name + "'>" + text + "</span>";
    return html;
  }

  // btn base_code
  function btn_html(class_name) {
    var html = "";
    html = "<a class='" + class_name + "' href=''>詳しく見る</a>";
    return html;
  }

  // br base_code
  function br_html(class_name) {
    var html = "";
    html = "<br class='" + class_name + "'>";
    return html;
  }

  tinymce.create('tinymce.plugins.original_tinymce_button', {
    init: function (ed, url) {
      ed.addButton('box', {
        // text : ボタンの表示名
        text: 'ボックス',
        // type: 'menubutton'にすると、プルダウンのようなメニューボタンを作成することができます。
        type: 'menubutton',
        menu: [
          {
            text: '赤',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_red'));
            }
          },
          {
            text: '青',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_blue'));
            }
          },
          {
            text: 'グレー',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_gray'));
            }
          },
          {
            text: '緑',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_green'));
            }
          },
          {
            text: 'オレンジ',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_orange'));
            }
          },
          {
            text: 'ピンク',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_pink'));
            }
          },
          {
            text: '黄色',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(box_html('box_style_yellow'));
            }
          },
        ]
      });
      ed.addButton('marker', {
        // text : ボタンの表示名
        text: 'マーカー',
        // type: 'menubutton'にすると、プルダウンのようなメニューボタンを作成することができます。
        type: 'menubutton',
        menu: [
          {
            text: '黄色',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-yellow', ed.selection.getContent()));
            }
          },
          {
            text: 'ピンク',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-pink', ed.selection.getContent()));
            }
          },
          {
            text: '青',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-blue', ed.selection.getContent()));
            }
          },
          {
            text: '緑',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-green', ed.selection.getContent()));
            }
          },
          {
            text: '紫',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-purple', ed.selection.getContent()));
            }
          },
          {
            text: 'オレンジ',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(marker_html('marker-orange', ed.selection.getContent()));
            }
          },
        ]
      });
      ed.addButton('button', {
        // text : ボタンの表示名
        text: 'ボタン',
        // type: 'menubutton'にすると、プルダウンのようなメニューボタンを作成することができます。
        type: 'menubutton',
        menu: [
          {
            text: '小',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(btn_html('btn-block__mini'));
            }
          },
          {
            text: '中',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(btn_html('btn-block'));
            }
          },
        ]
      });
      ed.addButton('br', {
        // text : ボタンの表示名
        text: '特殊改行',
        // type: 'menubutton'にすると、プルダウンのようなメニューボタンを作成することができます。
        type: 'menubutton',
        menu: [
          {
            text: 'スマホ未満のみ',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(br_html('show-sp'));
            }
          },
          {
            text: 'スマホ以上のみ',
            onclick: function() {
              // insertContentでカーソルのある位置に要素を追加します
              ed.insertContent(br_html('hide-sp'));
            }
          },
        ]
      });
    },
    createControl : function(n, cm) {
      return null;
    },
  });
  tinymce.PluginManager.add('original_tinymce_button_plugin', tinymce.plugins.original_tinymce_button);

})();
