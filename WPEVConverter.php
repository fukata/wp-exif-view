<?php
class WPEVConverter {
	/**
	 * 対象のセクションが存在しなかった場合に返す値
	 * @var string
	 */
	const EMPTY_VALUE = '';
	
	public static function conv_filename($exif, $options=array()){
		return self::isEmptyFileSeciton($exif) ? self::EMPTY_VALUE : $exif['FILE']['FileName'];
	}
	public static function conv_filesize($exif, $options=array()){
		return self::isEmptyFileSeciton($exif) ? self::EMPTY_VALUE : $exif['FILE']['FileSize'];
	}
	public static function conv_height($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['Height'];
	}
	public static function conv_width($exif, $options=array()){
		return self::isEmptyComputedSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['Width'];
	}
	public static function conv_mimetype($exif, $options=array()){
		return self::isEmptyComputedThumbnailSeciton($exif) ? self::EMPTY_VALUE : $exif['COMPUTED']['Thumbnail']['MimeType'];
	}
	public static function conv_datetime($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['DateTime'];
	}
	public static function conv_camera($exif, $options=array()){
		return self::isEmptyIfd0Section($exif) ? self::EMPTY_VALUE : $exif['IFD0']['Model'];
	}
	public static function conv_iso($exif, $options=array()){
		return self::isEmptyExifSection($exif) ? self::EMPTY_VALUE : $exif['EXIF']['ISOSpeedRatings'];
	}
	public static function conv_exposure_time($exif, $options=array()){
		return self::isEmptyExifSection($exif) ? self::EMPTY_VALUE : $exif['EXIF']['ExposureTime'];
	}
	public static function conv_fnumber($exif, $options=array()){
		return self::isEmptyExifSection($exif) ? self::EMPTY_VALUE : $exif['EXIF']['FNumber'];
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
}
?>