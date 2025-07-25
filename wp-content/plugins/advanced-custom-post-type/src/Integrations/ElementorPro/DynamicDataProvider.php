<?php

namespace ACPT\Integrations\ElementorPro;

use ACPT\Core\Models\Belong\BelongModel;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Core\Models\Meta\MetaGroupModel;
use ACPT\Core\Repository\MetaRepository;
use ACPT\Integrations\ElementorPro\Tags\ACPTColorTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTDateTimeTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTGalleryTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTImageTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTMediaTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTNumberTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTPhoneTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTTextTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTUnitOfMeasureTag;
use ACPT\Integrations\ElementorPro\Tags\ACPTUrlTag;

class DynamicDataProvider
{
	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var array
	 */
	private array $fields = [];

	/**
	 * @return DynamicDataProvider
	 */
	public static function getInstance()
	{
		if(self::$instance == null){
			self::$instance = new DynamicDataProvider();
			self::$instance->setFields();
		}

		return self::$instance;
	}

	/**
	 * DynamicDataProvider constructor.
	 */
	private function __construct(){}

	/**
	 * @return MetaFieldModel[]
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Register fields
	 */
	private function setFields()
	{
		try {
            $fieldGroups = MetaRepository::get([
                'clonedFields' => true
            ]);

            foreach ($fieldGroups as $fieldGroup){
                if(count($fieldGroup->getBelongs()) > 0){
                    foreach ($fieldGroup->getBelongs() as $belong){
                        $this->registerFields($fieldGroup, $belong);
                    }
                }
            }
		} catch (\Exception $exception){
			$this->fields = [];
		}
	}

    /**
     * @param MetaGroupModel $metaGroup
     * @param BelongModel $belong
     */
	private function registerFields(MetaGroupModel $metaGroup, BelongModel $belong)
	{
		$contextGroups = $this->contextGroups();

        foreach ($metaGroup->getBoxes() as $metaBox){
            foreach ($metaBox->getFields() as $boxFieldModel){
                foreach ($contextGroups as $tag => $fieldTypes){
                    if(in_array($boxFieldModel->getType(), $fieldTypes)){
                        $boxFieldModel->setBelongsAndFindLabels($belong);
                        $this->fields[$tag][] = $boxFieldModel;
                    }
                }
            }
        }
	}

	/**
	 * @return array
	 */
	private function contextGroups()
	{
		return [
			ACPTColorTag::class => [
				MetaFieldModel::COLOR_TYPE,
			],
			ACPTDateTimeTag::class => [
				MetaFieldModel::DATE_RANGE_TYPE,
				MetaFieldModel::DATE_TIME_TYPE,
				MetaFieldModel::DATE_TYPE,
				MetaFieldModel::TIME_TYPE,
			],
			ACPTGalleryTag::class => [
				MetaFieldModel::GALLERY_TYPE,
			],
			ACPTImageTag::class => [
				MetaFieldModel::IMAGE_TYPE,
			],
			ACPTMediaTag::class => [
				MetaFieldModel::FILE_TYPE,
				MetaFieldModel::VIDEO_TYPE,
			],
			ACPTNumberTag::class => [
				MetaFieldModel::NUMBER_TYPE,
				MetaFieldModel::RATING_TYPE,
			],
            ACPTPhoneTag::class => [
                MetaFieldModel::PHONE_TYPE,
            ],
			ACPTTextTag::class => [
				MetaFieldModel::ADDRESS_TYPE,
				MetaFieldModel::AUDIO_TYPE,
				MetaFieldModel::BARCODE_TYPE,
				MetaFieldModel::CHECKBOX_TYPE,
				MetaFieldModel::COUNTRY_TYPE,
				MetaFieldModel::EDITOR_TYPE,
				MetaFieldModel::EMAIL_TYPE,
				MetaFieldModel::HTML_TYPE,
                MetaFieldModel::NUMBER_TYPE,
				MetaFieldModel::PASSWORD_TYPE,
				MetaFieldModel::PHONE_TYPE,
				MetaFieldModel::QR_CODE_TYPE,
				MetaFieldModel::RADIO_TYPE,
				MetaFieldModel::RANGE_TYPE,
				MetaFieldModel::SELECT_TYPE,
				MetaFieldModel::SELECT_MULTI_TYPE,
				MetaFieldModel::TABLE_TYPE,
				MetaFieldModel::TEXT_TYPE,
				MetaFieldModel::TEXTAREA_TYPE,
				MetaFieldModel::URL_TYPE,
			],
			ACPTUrlTag::class => [
				MetaFieldModel::AUDIO_TYPE,
				MetaFieldModel::EMAIL_TYPE,
				MetaFieldModel::EMBED_TYPE,
				MetaFieldModel::PHONE_TYPE,
                MetaFieldModel::QR_CODE_TYPE,
				MetaFieldModel::URL_TYPE
			],
			ACPTUnitOfMeasureTag::class => [
				MetaFieldModel::CURRENCY_TYPE,
				MetaFieldModel::LENGTH_TYPE,
				MetaFieldModel::WEIGHT_TYPE,
			],
		];
	}
}
