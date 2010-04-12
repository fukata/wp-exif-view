<?php
require_once dirname(__FILE__).'/WPEVConverter.php';

class WPExifView {
	const PLUGIN_NAME = "WPExifView";
	// デフォルト値
	/**
	 * タグの始端文字列
	 * @var string
	 */
	const DEFAULT_TAG_START = "[#";
	
	/**
	 * タグの終端文字列
	 * @var string
	 */
	const DEFAULT_TAG_END = "]";
	
	/**
	 * テンプレート文字列
	 * @var string
	 */
	const DEFAULT_TEMPLATE ="<blockquote>
撮影日時：[#datetime]<br/>
カメラモデル：[#camera]<br/>
ISO値：[#iso]<br/>
露出値：[#exposure_time]<br/>
F値：[#exposure_time]<br/>
</blockquote>";
	
	/**
	 * 利用可能なタグ一覧
	 * @var array
	 */
	public static $AVAILABLE_TAGS = array(
		array("label"=>"ファイル名", "tag"=>"filename", "convert_method"=>"conv_filename"),
		array("label"=>"ファイルサイズ", "tag"=>"filesize", "convert_method"=>"conv_filesize"),
		array("label"=>"画像サイズ（縦）", "tag"=>"height", "convert_method"=>"conv_height"),
		array("label"=>"画像サイズ（横）", "tag"=>"width", "convert_method"=>"conv_width"),
		array("label"=>"MimeType", "tag"=>"mimetype", "convert_method"=>"conv_mimetype"),
		array("label"=>"撮影日時", "tag"=>"datetime", "convert_method"=>"conv_datetime"),
		array("label"=>"カメラモデル", "tag"=>"camera", "convert_method"=>"conv_camera"),
		array("label"=>"ISO値", "tag"=>"iso", "convert_method"=>"conv_iso"),
		array("label"=>"露出値", "tag"=>"exposure_time", "convert_method"=>"conv_exposure_time"),
		array("label"=>"F値", "tag"=>"fnumber", "convert_method"=>"conv_fnumber"),
	);
	
	// オプション項目
	/**
	 * テンプレート
	 * @var string
	 */
	const OPT_TEMPLATE = "wpev_template";
	
	/**
	 * EXIF情報を返す。
	 * 
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 * @return string
	 */
	public function doInsertExifData($atts, $content=null) {
		// オプションの設定
		$atts = shortcode_atts(array(
			'img'=>null,
		), $atts);
	
		// アップロードディレクトリ
		$upload_path = $this->getUploadDir();
	
		$img_path = $upload_path . "/" . $this->removeHeadSlash($atts['img']);
		if (!is_file($img_path)) {
			return $img_path . " is not exists.";
		}
		
		// exifデータの取得
		$exif = exif_read_data($img_path, 0, true);
		if (!$exif) {
			return "not have exif data.";
		}
		
		$html = get_option('wpev_template');
		foreach (self::$AVAILABLE_TAGS as $tag) {
			$html = str_replace($this->getTag($tag["tag"]), WPEVConverter::$tag["convert_method"]($exif), $html);
		}
		
		return $html;
	}
	
	/**
	 * 先頭のスラッシュ「/」を除去する。
	 * 
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	private function removeHeadSlash($value) {
		$len = strlen($value);
		if ($len<=0) {
			return $value;
		}
		
		$sub = substr($value, 0, 1);
		if ($sub=='/') {
			return substr($value, 1, $len-1);
		} else {
			return $value;
		}
	}
	
	/**
	 * 画像アップロードディレクトリを返す。
	 * 
	 * @return unknown_type
	 */
	private function getUploadDir() {
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		if ( empty($upload_path) )
			$dir = WP_CONTENT_DIR . '/uploads';
		else
			$dir = $upload_path;
	
		// $dir is absolute, $path is (maybe) relative to ABSPATH
		$dir = path_join( ABSPATH, $dir );
	
		if ( defined('UPLOADS') ) {
			$dir = ABSPATH . UPLOADS;
		}
		
		return $dir;
	}
	
	/**
	 * オプションメニューを追加する。
	 * 
	 * @return unknown_type
	 */
	public function pluginMenu() {
		add_options_page(self::PLUGIN_NAME.' Option', self::PLUGIN_NAME, 8, __FILE__, array($this,"pluginOptions"));
	}
	
	/**
	 * オプション設定画面のHTMLを生成する。
	 * 
	 * @return unknown_type
	 */
	public function pluginOptions() {
?>
<div class="wrap">
	<h2>ExifView</h2>
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="<?php echo self::getPageOptions() ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<p>テンプレート</p>
					<div>■利用可能な項目<br/>
					<ul>
						<?php foreach(self::$AVAILABLE_TAGS as $tag) { ?>
							<li><?php echo $tag["label"] ?>：<?php echo $this->getTag($tag["tag"]) ?></li>
						<?php } ?>
					</ul>
				</th>
				<td><textarea name="wpev_template" rows="15" cols="70"><?php echo self::getTemplate(); ?></textarea></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
<?php	
	}
	
	/**
	 * オプション設定画面での更新対象の項目を返す。
	 * 
	 * @return unknown_type
	 */
	private static function getPageOptions() {
		return self::OPT_TEMPLATE;
	}
	
	public static function getTemplate() {
		return get_option(self::OPT_TEMPLATE, self::DEFAULT_TEMPLATE);
	}
	
	/**
	 * タグを整形して返す。
	 * 
	 * @param unknown_type $tag
	 * @return unknown_type
	 */
	public function getTag($tag) {
		return $this->getTagStart() . trim($tag) . $this->getTagEnd();
	}
	
	/**
	 * タグの始端文字列を返す。
	 * 
	 * @return unknown_type
	 */
	private function getTagStart(){
		return self::DEFAULT_TAG_START;
	}
	
	/**
	 * タグの終端文字列を返す。
	 * @return unknown_type
	 */
	private function getTagEnd(){
		return self::DEFAULT_TAG_END;
	}
}
?>