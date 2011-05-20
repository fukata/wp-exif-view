<?php
require_once dirname(__FILE__).'/WPEVConverter.php';

class WPExifView {
	/**
	 * Plugin name
	 * @var string
	 */
	const PLUGIN_NAME = "WPExifView";

	/**
	 * Text domain
	 * @var string
	 */
	const TEXT_DOMAIN = "wpexifview";

	/**
	 * Language file dir path
	 * @var string
	 */
	private $languageDir;

	// Default values
	/**
	* Default short tag prefix
	* @var string
	*/
	const DEFAULT_TAG_START = "[#";

	/**
	 * Default short tag suffix
	 * @var string
	 */
	const DEFAULT_TAG_END = "]";

	/**
	 * Default template
	 * @var string
	 */
	private $defaultTemplate;

	/**
	 * Available tags
	 * @var array
	 */
	public static $AVAILABLE_TAGS = array(
	array("label"=>"FileName", "tag"=>"filename", "convert_method"=>"conv_filename", "note"=>""),
	array("label"=>"FileSize", "tag"=>"filesize", "convert_method"=>"conv_filesize", "note"=>""),
	array("label"=>"ImageHeight", "tag"=>"height", "convert_method"=>"conv_height", "note"=>""),
	array("label"=>"ImageWidth", "tag"=>"width", "convert_method"=>"conv_width", "note"=>""),
	array("label"=>"MimeType", "tag"=>"mimetype", "convert_method"=>"conv_mimetype", "note"=>""),
	array("label"=>"DateTime", "tag"=>"datetime", "convert_method"=>"conv_datetime", "note"=>""),
	array("label"=>"Maker", "tag"=>"maker", "convert_method"=>"conv_maker", "note"=>""),
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

	// Option items
	/**
	* Template
	* @var string
	*/
	const OPT_TEMPLATE = "wpev_template";

	/**
	 * Construct
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
	 * Insert EXIF information in post
	 *
	 * @param assoc $atts short tag options
	 * @param string $content post content
	 * @return string
	 */
	public function doInsertExifData($atts, $content=null) {
		$this->load_plugin_textdomain();

		// Setting options
		$atts = shortcode_atts(array(
			'img'=>null,
		), $atts);

		// Upload dir path
		$upload_path = $this->getUploadDir();

		$img_path = $upload_path . "/" . ltrim(trim($atts['img']), '/');
		if (!is_file($img_path)) {
			return $img_path . " is not exists.";
		}

		// Exif information
		$exif = exif_read_data($img_path, 0, true);
		if (!$exif) {
			return __("Not have exif data", self::TEXT_DOMAIN);
		}

		// Convert template
		$html = ($content!=null) ? $content : $this->getTemplate('wpev_template');
		foreach (self::$AVAILABLE_TAGS as $tag) {
			$html = str_replace($this->getTag($tag["tag"]), WPEVConverter::$tag["convert_method"]($exif), $html);
		}

		return $html;
	}

	/**
	 * Return upload dir path
	 *
	 * @see ./wp-includes/functions.php:2146:function wp_upload_dir( $time = null ) {
	 *
	 * @return string upload dir path
	 */
	private function getUploadDir() {
		global $switched;
		$siteurl = get_option( 'siteurl' );
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		$main_override = is_multisite() && defined( 'MULTISITE' ) && is_main_site();
		if ( empty($upload_path) ) {
			$dir = WP_CONTENT_DIR . '/uploads';
		} else {
			$dir = $upload_path;
			if ( 'wp-content/uploads' == $upload_path ) {
				$dir = WP_CONTENT_DIR . '/uploads';
			} elseif ( 0 !== strpos($dir, ABSPATH) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$dir = path_join( ABSPATH, $dir );
			}
		}

		if ( !$url = get_option( 'upload_url_path' ) ) {
			if ( empty($upload_path) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) )
			$url = WP_CONTENT_URL . '/uploads';
			else
			$url = trailingslashit( $siteurl ) . $upload_path;
		}

		if ( defined('UPLOADS') && !$main_override && ( !isset( $switched ) || $switched === false ) ) {
			$dir = ABSPATH . UPLOADS;
			$url = trailingslashit( $siteurl ) . UPLOADS;
		}

		if ( is_multisite() && !$main_override && ( !isset( $switched ) || $switched === false ) ) {
			if ( defined( 'BLOGUPLOADDIR' ) )
			$dir = untrailingslashit(BLOGUPLOADDIR);
			$url = str_replace( UPLOADS, 'files', $url );
		}

		return $dir;
	}

	/**
	 * Add plugin option menu
	 */
	public function pluginMenu() {
		add_options_page(self::PLUGIN_NAME.' Option', self::PLUGIN_NAME, 8, __FILE__, array($this,"pluginOptions"));
	}

	/**
	 * Draw option setting page html
	 */
	public function pluginOptions() {
		$this->load_plugin_textdomain();
		?>
<div class="wrap">
	<h2>
	<?php echo self::PLUGIN_NAME ?>
	</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
		<input type="hidden" name="action" value="update" /> <input
			type="hidden" name="page_options"
			value="<?php echo self::getPageOptions() ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<p>
					<?php echo _e('Template') ?>
					</p>
					<div>
					<?php echo _e('Available Items') ?>
						<br />
						<ul>
						<?php foreach(self::$AVAILABLE_TAGS as $tag) { ?>
							<li><?php echo _e($tag["label"], self::TEXT_DOMAIN) ?>：<?php echo $this->getTag($tag["tag"]) ?>
							</li>
							<?php } ?>
						</ul>
				
				</th>
				<td><textarea name="wpev_template" rows="15" cols="70">
				<?php echo $this->getTemplate(); ?>
					</textarea></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
				<?php
	}

	/**
	 * Return update option item list
	 *
	 * @return string
	 */
	private static function getPageOptions() {
		return self::OPT_TEMPLATE;
	}

	/**
	 * Return option template value
	 *
	 * @return string
	 */
	public function getTemplate() {
		return get_option(self::OPT_TEMPLATE, $this->defaultTemplate);
	}

	/**
	 * Return format tag
	 *
	 * @param string $tag
	 * @return string
	 */
	public function getTag($tag) {
		return $this->getTagStart() . trim($tag) . $this->getTagEnd();
	}

	/**
	 * Return tag prefix
	 *
	 * @return string
	 */
	private function getTagStart(){
		return self::DEFAULT_TAG_START;
	}

	/**
	 * Return tag suffix
	 *
	 * @return string
	 */
	private function getTagEnd(){
		return self::DEFAULT_TAG_END;
	}

	/**
	 * load text domain
	 * @return void
	 */
	private function load_plugin_textdomain() {
		load_plugin_textdomain(self::TEXT_DOMAIN, $this->languageDir);
	}
}
?>