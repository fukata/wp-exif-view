<?php
require_once dirname(__FILE__).'/WPEVConverter.php';

class WPExifView {
	/**
	 * プラグイン名
	 * @var string
	 */
	const PLUGIN_NAME = "WPExifView";
	
	/**
	 * テキストドメイン
	 * @var string
	 */
	const TEXT_DOMAIN = "wpexifview";
	
	/**
	 * 言語ファイル格納ディレクトリ
	 * @var string
	 */
	private $languageDir;
	
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
	private $defaultTemplate;

	/**
	 * 利用可能なタグ一覧
	 * @var array
	 */
	public static $AVAILABLE_TAGS = array(
		array("label"=>"FileName", "tag"=>"filename", "convert_method"=>"conv_filename", "note"=>""),
		array("label"=>"FileSize", "tag"=>"filesize", "convert_method"=>"conv_filesize", "note"=>""),
		array("label"=>"ImageHeight", "tag"=>"height", "convert_method"=>"conv_height", "note"=>""),
		array("label"=>"ImageWidth", "tag"=>"width", "convert_method"=>"conv_width", "note"=>""),
		array("label"=>"MimeType", "tag"=>"mimetype", "convert_method"=>"conv_mimetype", "note"=>""),
		array("label"=>"DateTime", "tag"=>"datetime", "convert_method"=>"conv_datetime", "note"=>""),
		array("label"=>"CameraModel", "tag"=>"camera", "convert_method"=>"conv_camera", "note"=>""),
		array("label"=>"Lens", "tag"=>"lens", "convert_method"=>"conv_lens", "note"=>""),
		array("label"=>"ISO", "tag"=>"iso", "convert_method"=>"conv_iso", "note"=>""),
		array("label"=>"ExposureTime", "tag"=>"exposure_time", "convert_method"=>"conv_exposure_time", "note"=>""),
		array("label"=>"FNumber", "tag"=>"fnumber", "convert_method"=>"conv_fnumber"),
		array("label"=>"CCDWidth", "tag"=>"ccdwidth", "convert_method"=>"conv_ccd_width"),
		array("label"=>"UserComment", "tag"=>"usercomment", "convert_method"=>"conv_user_comment", "note"=>""),
		array("label"=>"Software", "tag"=>"software", "convert_method"=>"conv_software", "note"=>""),
		array("label"=>"Artist", "tag"=>"artist", "convert_method"=>"conv_artist", "note"=>""),
		array("label"=>"Copyright", "tag"=>"copyright", "convert_method"=>"conv_copyright", "note"=>""),
		array("label"=>"FirmwareVersion", "tag"=>"firmware_version", "convert_method"=>"conv_firmware_version", "note"=>""),
	);
	
	// オプション項目
	/**
	 * テンプレート
	 * @var string
	 */
	const OPT_TEMPLATE = "wpev_template";
	
	/**
	 * コンストラクタ
	 * @return unknown_type
	 */
	public function __construct() {
		$this->languageDir = dirname(__FILE__) . "/language/";
		$this->load_plugin_textdomain();
		
		// デフォルトテンプレートの整形
		$this->defaultTemplate = "";
		$this->defaultTemplate .= "<blockquote>";
		$this->defaultTemplate .= __("DateTime", self::TEXT_DOMAIN)."：[#datetime]<br/>";
		$this->defaultTemplate .= __("Model", self::TEXT_DOMAIN)."：[#camera]<br/>";
		$this->defaultTemplate .= __("ISO", self::TEXT_DOMAIN)."：[#iso]<br/>";
		$this->defaultTemplate .= __("ExposureTime", self::TEXT_DOMAIN)."：[#exposure_time]<br/>";
		$this->defaultTemplate .= __("FNumber", self::TEXT_DOMAIN)."：[#fnumber]";
		$this->defaultTemplate .= "</blockquote>";
	}
	
	/**
	 * EXIF情報を返す。
	 * 
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 * @return string
	 */
	public function doInsertExifData($atts, $content=null) {
		$this->load_plugin_textdomain();
		
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
			return __("Not have exif data", self::TEXT_DOMAIN);
		}
		
		$html = ($content!=null) ? $content : $this->getTemplate('wpev_template');
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
		$this->load_plugin_textdomain();
?>
<div class="wrap">
	<h2><?php echo self::PLUGIN_NAME ?></h2>
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="<?php echo self::getPageOptions() ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<p><?php echo _e('Template') ?></p>
					<div><?php echo _e('Available Items') ?><br/>
					<ul>
						<?php foreach(self::$AVAILABLE_TAGS as $tag) { ?>
							<li><?php echo _e($tag["label"], self::TEXT_DOMAIN) ?>：<?php echo $this->getTag($tag["tag"]) ?></li>
						<?php } ?>
					</ul>
				</th>
				<td><textarea name="wpev_template" rows="15" cols="70"><?php echo $this->getTemplate(); ?></textarea></td>
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
	 * @return string
	 */
	private static function getPageOptions() {
		return self::OPT_TEMPLATE;
	}
	
	/**
	 * テンプレートの値を返す。
	 * 
	 * @return string
	 */
	public function getTemplate() {
		return get_option(self::OPT_TEMPLATE, $this->defaultTemplate);
	}
	
	/**
	 * タグを整形して返す。
	 * 
	 * @param string $tag
	 * @return string
	 */
	public function getTag($tag) {
		return $this->getTagStart() . trim($tag) . $this->getTagEnd();
	}
	
	/**
	 * タグの始端文字列を返す。
	 * 
	 * @return string
	 */
	private function getTagStart(){
		return self::DEFAULT_TAG_START;
	}
	
	/**
	 * タグの終端文字列を返す。
	 * @return string
	 */
	private function getTagEnd(){
		return self::DEFAULT_TAG_END;
	}
	
	/**
	 * テキストドメインのロード
	 * @return void
	 */
	private function load_plugin_textdomain() {
		load_plugin_textdomain(self::TEXT_DOMAIN, $this->languageDir);
	}
}
?>