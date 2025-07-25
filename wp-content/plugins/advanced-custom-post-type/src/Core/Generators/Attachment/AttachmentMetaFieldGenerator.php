<?php

namespace ACPT\Core\Generators\Attachment;

use ACPT\Constants\MetaTypes;
use ACPT\Core\Generators\AbstractGenerator;
use ACPT\Core\Generators\Meta\Fields\AbstractField;
use ACPT\Core\Models\Meta\MetaFieldModel;
use ACPT\Utils\Data\Meta;

class AttachmentMetaFieldGenerator extends AbstractGenerator
{
	/**
	 * @var MetaFieldModel
	 */
	private MetaFieldModel $fieldModel;

	/**
	 * @var
	 */
	private $attachmentId;

	/**
	 * AttachmentMetaFieldGenerator constructor.
	 *
	 * @param MetaFieldModel $fieldModel
	 * @param $attachmentId
	 */
	public function __construct(MetaFieldModel $fieldModel, $attachmentId)
	{
		$this->fieldModel = $fieldModel;
		$this->attachmentId = $attachmentId;
	}

	/**
	 * @return AbstractField|null
	 */
	public function generate()
	{
		return $this->getAttachmentMetaField();
	}

	/**
	 * @return AbstractField|null
	 */
	private function getAttachmentMetaField()
	{
		$className = 'ACPT\\Core\\Generators\\Meta\\Fields\\'.$this->fieldModel->getType().'Field';
        $value = null;

		if(class_exists($className)){

            if(!empty($this->attachmentId)){
                $value = Meta::fetch($this->attachmentId, MetaTypes::MEDIA, $this->fieldModel->getDbName());
            }

			/** @var AbstractField $instance */
			$instance = new $className($this->fieldModel, MetaTypes::MEDIA, $this->attachmentId);

            if($value !== '' and $value !== null){
                $instance->setValue($value);
            }

			return $instance;
		}

		return null;
	}
}