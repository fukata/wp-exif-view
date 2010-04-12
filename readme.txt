=== WPExifView ===
Contributors: tatsuya
Donate link: http://fukata.org/
Tags: image,exif
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: version-1.0.0

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
* カメラモデル
* ISO値
* 露出値
* F値

== Installation ==

1. 解凍後、フォルダ「wp-exif-view/」をディレクトリ「/wp-content/plugins/」にアップロードする。
2. 管理画面よりプラグイン「WPExifView」をアクティベートを行う。
3. 記事本文内に[exif img="[画像ファイルパス]"]を埋め込む。imgには画像アップロードディレクトリ以下のパスを指定すること。

== Frequently Asked Questions ==

現在なし

== Screenshots ==

1.Setting template.

== Changelog ==

= 1.0.0 =
* テンプレートを登録できるように変更

== Upgrade Notice ==

= 1.0.0 =
This version is first release.