<?php
class WPEVConverter {
	/**
	 * Default value when target exif section does not exists.
	 * @var string
	 */
	const EMPTY_VALUE = '';

	/**
	 * return converted FILE.FileName value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_filename($exif, $options=array()){
		return self::getSectionValue($exif, 'FILE', 'FileName');
	}

	/**
	 * return converted FILE.FileSize value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_filesize($exif, $options=array()){
		return self::getSectionValue($exif, 'FILE', 'FileSize');
	}

	/**
	 * return converted COMPUTED.Height value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_height($exif, $options=array()){
		return self::getSectionValue($exif, 'COMPUTED', 'Height');
	}

	/**
	 * return converted COMPUTED.Width value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_width($exif, $options=array()){
		return self::getSectionValue($exif, 'COMPUTED', 'Width');
	}

	/**
	 * return converted FILE.MimeType value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_mimetype($exif, $options=array()){
		return self::getSectionValue($exif, 'FILE', 'MimeType');
	}

	/**
	 * return converted IFD0.DateTime value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_datetime($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'DateTime');
	}

	/**
	 * return converted EXIF.DateTimeOriginal value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_taken_date($exif, $options=array()){
		return self::getSectionValue($exif, 'EXIF', 'DateTimeOriginal');
	}

	/**
	 * return converted IFD0.Make value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_maker($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'Make');
	}

	/**
	 * return converted IFD0.Model value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_camera($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'Model');
	}

	/**
	 * return converted MAKERNOTE.UndefinedTag:0x0095 value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_lens($exif, $options=array()){
		$lens = self::EMPTY_VALUE;
		// for Canon EOS 70D
		$lens = self::getSectionValue($exif, 'EXIF', 'UndefinedTag:0xA434');
		// for General
		if (!$lens) $lens = self::isEmptyMakerNoteSection($exif) ? self::EMPTY_VALUE : self::getSectionValue($exif, 'MAKERNOTE', 'UndefinedTag:0x0095');

		return $lens;
	}

	/**
	 * return converted EXIF.ISOSpeedRatings value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_iso($exif, $options=array()){
		return self::getSectionValue($exif, 'EXIF', 'ISOSpeedRatings');
	}

	/**
	 * return converted EXIF.ExposureTime value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_exposure_time($exif, $options=array()){
		if (! self::isEmptyExifSection($exif)) {
			$splitedExposure = explode('/', self::getSectionValue($exif, 'EXIF', 'ExposureTime'));
			if ($splitedExposure[0] == 1) {
				$exposure = self::getSectionValue($exif, 'EXIF', 'ExposureTime');
			} else if (is_numeric($splitedExposure[0]) && is_numeric($splitedExposure[1])) {
				$_exposure = $splitedExposure[1] / $splitedExposure[0];
				// ShutterSpeed over 1 seconds
				if ($_exposure < 1) {
					$_e = $splitedExposure[0] / $splitedExposure[1];
					$exposure = $_e . '/1';
				} else {
					$exposure = '1/' . $_exposure;
				}
			}
		} else {
			$exposure = self::EMPTY_VALUE;
		}
		return $exposure;
	}

	/**
	 * return converted COMPUTED.ApertureFNumber value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_fnumber($exif, $options=array()){
		if (self::isEmptyComputedSeciton($exif)) {
			return self::EMPTY_VALUE;
		} else {
			$splitedFNumber = explode('/', self::getSectionValue($exif, 'COMPUTED', 'ApertureFNumber'));
			return count($splitedFNumber) == 2 ? $splitedFNumber[1] : self::getSectionValue($exif, 'COMPUTED', 'ApertureFNumber');
		}
	}

	/**
	 * return converted COMPUTED.CCDWidth value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_ccd_width($exif, $options=array()){
		return self::getSectionValue($exif, 'COMPUTED', 'CCDWidth');
	}

	/**
	 * return converted COMPUTED.UserComment value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_user_comment($exif, $options=array()){
		return self::getSectionValue($exif, 'COMPUTED', 'UserComment');
	}

	/**
	 * return converted IFD0.Software value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_software($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'Software');
	}

	/**
	 * return converted IFD0.Artist value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_artist($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'Artist');
	}

	/**
	 * return converted IFD0.Copyright value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_copyright($exif, $options=array()){
		return self::getSectionValue($exif, 'IFD0', 'Copyright');
	}

	/**
	 * return converted MAKERNOTE.FirmwareVersion value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_firmware_version($exif, $options=array()){
		return self::isEmptyMakerNoteSection($exif) ? self::EMPTY_VALUE : $exif['MAKERNOTE']['FirmwareVersion'];
	}

	private static function isEmptyFileSeciton($exif) {
		return self::isEmptySection($exif, 'FILE');
	}
	private static function isEmptyComputedSeciton($exif) {
		return self::isEmptySection($exif, 'COMPUTED');
	}
	private static function isEmptyComputedThumbnailSeciton($exif) {
		return self::isEmptyComputedSeciton($exif) || empty($exif['COMPUTED']['Thumnail']);
	}
	private static function isEmptyIfd0Section($exif) {
		return self::isEmptySection($exif, 'IFD0');
	}
	private static function isEmptyExifSection($exif) {
		return self::isEmptySection($exif, 'EXIF');
	}
	private static function isNotEmptyExifSection($exif) {
		return !self::isEmptyExifSection($exif);
	}
	private static function isEmptyMakerNoteSection($exif) {
		return self::isEmptySection($exif, 'MAKERNOTE');
	}

	private static function isEmptySection($exif, $section) {
		return !isset($exif[$section]) || empty($exif[$section]);
	}
	public static function getSectionValue($exif, $section, $item, $default=self::EMPTY_VALUE) {
		if (!isset($exif[$section])) {
			return $default;
		}

		if (!isset($exif[$section][$item])) {
			return $default;
		}

		return $exif[$section][$item];
	}
}
?>
