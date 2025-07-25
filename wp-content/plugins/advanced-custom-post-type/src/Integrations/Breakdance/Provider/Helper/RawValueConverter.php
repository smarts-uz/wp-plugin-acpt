<?php

namespace ACPT\Integrations\Breakdance\Provider\Helper;

use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Meta\MetaFieldModel;

class RawValueConverter
{
	/**
	 * @param $rawValue
	 * @param $fieldType
	 * @param $attributes
	 *
	 * @return array|string|null
	 */
	public static function convert($rawValue, $fieldType, $attributes)
	{
		try {
			switch ($fieldType){

				case MetaFieldModel::RATING_TYPE:

					if(empty($rawValue)){
						return null;
					}

					$size = isset($attributes['size']) ? $attributes['size'] : null;
					$rawValue = Strings::renderStars($rawValue, $size);
					break;

				case MetaFieldModel::GALLERY_TYPE:

					if(empty($rawValue)){
						return null;
					}

					if(!is_array($rawValue)){
						return null;
					}

					return $rawValue;

				case MetaFieldModel::LIST_TYPE:

					if(!is_array($rawValue)){
						return null;
					}

					$list = '<ul>';

					foreach ($rawValue as $item){
						$list .= '<li>'.$item.'</li>';
					}

					$list .= '</ul>';

					$rawValue = $list;

					break;
			}

			return $rawValue;
		} catch (\Exception $exception){
			return $rawValue;
		}
	}
}