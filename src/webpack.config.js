//pathの読み込み
const path = require("path")
//パッケージのライセンス情報をjsファイルの中に含める
const TerserPlugin = require("terser-webpack-plugin")
//mini-css-extract-plugin の読み込み
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
// browser-sync-webpack-pluginの読み込み
const BrowserSyncPlugin = require('browser-sync-webpack-plugin')
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts')

// 本番環境のときはsoucemapを出力させない設定
const enabledSourceMap = process.env.NODE_ENV !== "production"

const app = {
  // 読み込み先（srcの中のjsフォルダのinit.jsを読み込む）
  entry: {
    'bundle': path.resolve(__dirname, "./js/app.js"),
    'common' : path.resolve(__dirname, "./scss/common.scss"),
  },
  // cache: {
  //   type: 'filesystem',
  //   buildDependencies: {
  //     config: [__filename]
  //   }
  // },
  //出力先（distの中のjsフォルダへinit.jsを出力）
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "../js/")
  },

  //パッケージのライセンス情報をjsファイルの中に含める
  optimization: {
    minimizer: [
      new TerserPlugin({
        extractComments: false
      })
    ]
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/env"]
          }
        }
      },
      {
        test: /\.vue$/,
        loader: "vue-loader"
      },
      {
        // 対象となるファイルの拡張子(scssかsassかcss)
        test: /\.(sa|sc|c)ss$/,
        // Sassファイルの読み込みとコンパイル
        use: [
          // CSSファイルを抽出するように MiniCssExtractPlugin のローダーを指定
          {
            loader: MiniCssExtractPlugin.loader
          },
          // CSSをバンドルするためのローダー
          {
            loader: "css-loader",
            options: {
              // CSS内のurl()メソッドの取り込みを禁止
              url: false,
              // production モードでなければソースマップを有効に
              sourceMap: enabledSourceMap,
              // postcss-loader と sass-loader の場合は2を指定
              importLoaders: 2
              // 0 => no loaders (default)
              // 1 => postcss-loader
              // 2 => postcss-loader, sass-loader
            }
          },
          // PostCSS（autoprefixer）の設定
          {
            loader: "postcss-loader",
            options: {
              // production モードでなければソースマップを有効に
              sourceMap: enabledSourceMap,
              postcssOptions: {
                // ベンダープレフィックスを自動付与
                plugins: [require("autoprefixer")({ grid: true })]
              }
            }
          },
          // Sass を CSS へ変換するローダー
          {
            loader: "sass-loader",
            options: {
              // dart-sass を優先
              implementation: require("sass"),
              //  production モードでなければソースマップを有効に
              sourceMap: enabledSourceMap
            }
          }
        ]
      }
    ]
  },
  resolve: {
    extensions: ['.js', '.vue', '.scss', '.sass'],
    modules: [path.resolve(__dirname, "./node_modules/")],
    alias: {
    // vue.js のビルドを指定する
      // slick: 'slick-carousel/slick/',
      vue: 'vue/dist/vue.js'
    }
  },
  target: "web",
 //webpackの中に画像の圧縮処理など、重い処理を含めるとwarningが表示されます。それを回避する設定
  performance: {
    hints: false,
    maxEntrypointSize: 512000,
    maxAssetSize: 512000
  },
  stats: {
    children: true,
  },
  //プラグインの設定
  plugins: [
    new RemoveEmptyScriptsPlugin(),
    new BrowserSyncPlugin({
      //browser
      host: 'localhost',
      port: 3000,
      proxy: 'http://localhost/',
      files: [
        "../css/*.css",
        "../js/*.js",
        "../*.php",
        "../*/*.php",
      ],
    }),
    new MiniCssExtractPlugin({ // distの中にあるcssフォルダにstyle.cssを出力
      filename: "../css/[name].css"
    }),
  ],
  //source-map タイプのソースマップを出力
  devtool: "source-map",
  // node_modules を監視（watch）対象から除外
  watchOptions: {
    ignored: /node_modules/ //正規表現で指定
  }
}

module.exports = app
