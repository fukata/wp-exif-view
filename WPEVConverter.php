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
		return self::isEmptyFileSeciton($exif) ? self::EMPTY_VALUE : $exif['FILE']['FileName'];
	}

	/**
	 * return converted FILE.FileSize value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_filesize($exif, $options=array()){
		return self::isEmptyFileSeciton($exif) ? self::EMPTY_VALUE : $exif['FILE']['FileSize'];
	}

	/**
	 * return converted COMPUTED.Height value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_height($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['Height'];
	}

	/**
	 * return converted COMPUTED.Width value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_width($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['Width'];
	}

	/**
	 * return converted FILE.MimeType value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_mimetype($exif, $options=array()){
		return self::isEmptyFileSeciton($exif) ? self::EMPTY_VALUE : $exif['FILE']['MimeType'];
	}

	/**
	 * return converted IFD0.DateTime value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_datetime($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['DateTime'];
	}

	/**
	 * return converted EXIF.DateTimeOriginal value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_taken_date($exif, $options=array()){
		return self::isEmptyExifSection($exif) ? self::EMPTY_VALUE : $exif['EXIF']['DateTimeOriginal'];
	}

	/**
	 * return converted IFD0.Make value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_maker($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Make'];
	}

	/**
	 * return converted IFD0.Model value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_camera($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Model'];
	}

	/**
	 * return converted MAKERNOTE.UndefinedTag:0x0095 value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_lens($exif, $options=array()){
		return self::isEmptyMakerNoteSection($exif) ? self::EMPTY_VALUE : $exif['MAKERNOTE']['UndefinedTag:0x0095'];
	}

	/**
	 * return converted EXIF.ISOSpeedRatings value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_iso($exif, $options=array()){
		return self::isEmptyExifSection($exif) ? self::EMPTY_VALUE : $exif['EXIF']['ISOSpeedRatings'];
	}

	/**
	 * return converted EXIF.ExposureTime value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_exposure_time($exif, $options=array()){
		if (! self::isEmptyExifSection($exif)) {
			$splitedExposure = explode('/', $exif['EXIF']['ExposureTime']);
			if ($splitedExposure[0] == 1) {
				$exposure = $exif['EXIF']['ExposureTime'];
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
			$splitedFNumber = explode('/', $exif['COMPUTED']['ApertureFNumber']);
			return count($splitedFNumber) == 2 ? $splitedFNumber[1] : $exif['COMPUTED']['ApertureFNumber'];
		}
	}

	/**
	 * return converted COMPUTED.CCDWidth value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_ccd_width($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['CCDWidth'];
	}

	/**
	 * return converted COMPUTED.UserComment value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_user_comment($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['UserComment'];
	}

	/**
	 * return converted IFD0.Software value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_software($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Software'];
	}

	/**
	 * return converted IFD0.Artist value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_artist($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Artist'];
	}

	/**
	 * return converted IFD0.Copyright value
	 * @param array $exif
	 * @param array $options
	 */
	public static function conv_copyright($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Copyright'];
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
		return empty($exif['FILE']);
	}
	private static function isEmptyComputedSeciton($exif) {
		return empty($exif['COMPUTED']);
	}
	private static function isEmptyComputedThumbnailSeciton($exif) {
		return self::isEmptyComputedSeciton($exif) || empty($exif['COMPUTED']['Thumnail']);
	}
	private static function isEmptyIfd0Section($exif) {
		return empty($exif['IFD0']);
	}
	private static function isEmptyExifSection($exif) {
		return empty($exif['EXIF']);
	}
	private static function isEmptyMakerNoteSection($exif) {
		return empty($exif['MAKERNOTE']);
	}
}
?>