<?php
/**
 * RKImageProxyStateFixture
 *
 */
class RKImageProxyStateFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'RKImageProxyState';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'modelId' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'versionUuid' => array('type' => 'string', 'null' => true),
		'versionId' => array('type' => 'integer', 'null' => true),
		'thumbnailsCurrent' => array('type' => 'integer', 'null' => true),
		'fullSizePreviewUpToDate' => array('type' => 'integer', 'null' => true),
		'thumbnailCacheIndex' => array('type' => 'integer', 'null' => true),
		'thumbnailRendered' => array('type' => 'integer', 'null' => true),
		'previewRendered' => array('type' => 'integer', 'null' => true),
		'previewJpegHeight' => array('type' => 'integer', 'null' => true),
		'previewJpegWidth' => array('type' => 'integer', 'null' => true),
		'previewJpegRotation' => array('type' => 'integer', 'null' => true),
		'previewToMasterRotation' => array('type' => 'integer', 'null' => true),
		'fullSizePreviewPath' => array('type' => 'string', 'null' => true),
		'fullSizePreviewChangeDate' => array('type' => 'timestamp', 'null' => true),
		'thumbnailPath' => array('type' => 'string', 'null' => true),
		'thumbnailRotation' => array('type' => 'integer', 'null' => true),
		'thumbnailHeight' => array('type' => 'integer', 'null' => true),
		'thumbnailWidth' => array('type' => 'integer', 'null' => true),
		'previewFilesize' => array('type' => 'integer', 'null' => true),
		'miniThumbnailRotation' => array('type' => 'integer', 'null' => true),
		'miniThumbnailHeight' => array('type' => 'integer', 'null' => true),
		'miniThumbnailWidth' => array('type' => 'integer', 'null' => true),
		'tinyThumbnailRotation' => array('type' => 'integer', 'null' => true),
		'tinyThumbnailHeight' => array('type' => 'integer', 'null' => true),
		'tinyThumbnailWidth' => array('type' => 'integer', 'null' => true),
		'microThumbnailRotation' => array('type' => 'integer', 'null' => true),
		'miniThumbnailPath' => array('type' => 'string', 'null' => true),
		'miniThumbnailFilesize' => array('type' => 'integer', 'null' => true),
		'miniThumbnailsCurrent' => array('type' => 'integer', 'null' => true),
		'tinyThumbnailsCurrent' => array('type' => 'integer', 'null' => true),
		'thumbnailFilesize' => array('type' => 'integer', 'null' => true),
		'faceTilePath' => array('type' => 'string', 'null' => true),
		'indexes' => array(
			'RKImageProxyState_tinyThumbnailsCurrent_index' => array('column' => 'tinyThumbnailsCurrent', 'unique' => false),
			'RKImageProxyState_miniThumbnailsCurrent_index' => array('column' => 'miniThumbnailsCurrent', 'unique' => false),
			'RKImageProxyState_thumbnailRendered_index' => array('column' => 'thumbnailRendered', 'unique' => false),
			'RKImageProxyState_fullSizePreviewUpToDate_index' => array('column' => 'fullSizePreviewUpToDate', 'unique' => false),
			'RKImageProxyState_thumbnailsCurrent_index' => array('column' => 'thumbnailsCurrent', 'unique' => false),
			'RKImageProxyState_versionId_index' => array('column' => 'versionId', 'unique' => false),
			'RKImageProxyState_versionUuid_index' => array('column' => 'versionUuid', 'unique' => false)
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'modelId' => 1,
			'versionUuid' => 'Lorem ipsum dolor sit amet',
			'versionId' => 1,
			'thumbnailsCurrent' => 1,
			'fullSizePreviewUpToDate' => 1,
			'thumbnailCacheIndex' => 1,
			'thumbnailRendered' => 1,
			'previewRendered' => 1,
			'previewJpegHeight' => 1,
			'previewJpegWidth' => 1,
			'previewJpegRotation' => 1,
			'previewToMasterRotation' => 1,
			'fullSizePreviewPath' => 'Lorem ipsum dolor sit amet',
			'fullSizePreviewChangeDate' => 1418759196,
			'thumbnailPath' => 'Lorem ipsum dolor sit amet',
			'thumbnailRotation' => 1,
			'thumbnailHeight' => 1,
			'thumbnailWidth' => 1,
			'previewFilesize' => 1,
			'miniThumbnailRotation' => 1,
			'miniThumbnailHeight' => 1,
			'miniThumbnailWidth' => 1,
			'tinyThumbnailRotation' => 1,
			'tinyThumbnailHeight' => 1,
			'tinyThumbnailWidth' => 1,
			'microThumbnailRotation' => 1,
			'miniThumbnailPath' => 'Lorem ipsum dolor sit amet',
			'miniThumbnailFilesize' => 1,
			'miniThumbnailsCurrent' => 1,
			'tinyThumbnailsCurrent' => 1,
			'thumbnailFilesize' => 1,
			'faceTilePath' => 'Lorem ipsum dolor sit amet'
		),
	);

}
