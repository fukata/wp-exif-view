=== WPExifView ===
Contributors: tatsuya
Donate link: http://fukata.org/
Tags: images,exif
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.2.2

画像のEXIF情報を記事本文内に埋め込む。

== Description ==

当プラグインは、本文記事内にショートコードで指定することによりアップロードされた画像ファイルのEXIF情報を埋め込む。

現在指定可能な項目一覧

* ファイル名
* ファイルサイズ
* 画像サイズ（縦）
* 画像サイズ（横）
* MimeType
* 撮影日時
* カメラメーカー
* カメラモデル
* レンズ名
* ISO値
* 露出値
* F値
* CCDWidth
* ユーザコメント
* ソフトウェア
* アーティスト
* Copyright
* ファームウェアバージョン

== Installation ==

1. 解凍後、フォルダ「wp-exif-view/」をディレクトリ「/wp-content/plugins/」にアップロードする。
2. 管理画面よりプラグイン「WPExifView」をアクティベートを行う。
3. 記事本文内に[exif img="[画像ファイルパス]"]を埋め込む。imgには画像アップロードディレクトリ以下のパスを指定すること。

== Frequently Asked Questions ==

現在なし

== Screenshots ==

1. Setting template
2. Setting template for post

== Changelog ==
= 1.2.2 =
* 利用可能項目に「Maker」を追加

= 1.2.1 =
* 利用可能項目に「Lens」を追加

= 1.2.0 =
* 利用可能項目の追加
* 記事毎にテンプレートを設定できるように変更

= 1.1.0 =
* 管理画面の国際化

= 1.0.0 =
* テンプレートを登録できるように変更

== Upgrade Notice ==

= 1.0.0 =
This version is first release.