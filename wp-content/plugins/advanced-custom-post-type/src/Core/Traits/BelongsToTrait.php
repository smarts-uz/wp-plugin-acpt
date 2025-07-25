<?php

namespace ACPT\Core\Traits;

use ACPT\Constants\BelongsTo;
use ACPT\Constants\MetaTypes;
use ACPT\Constants\Operator;
use ACPT\Core\Helper\Strings;
use ACPT\Core\Models\Belong\BelongModel;
use ACPT\Utils\PHP\Logics;

trait BelongsToTrait
{
	/**
	 * @param string $belongsTo
	 * @param string|null $operator
	 * @param null $find
	 *
	 * @return bool
	 */
	public function belongsTo(string $belongsTo, ?string $operator = null, $find = null): bool
	{
		if(!method_exists(static::class, 'getBelongs')){
			return false;
		}

		try {
			/** @var BelongModel[] $belongs */
			$belongs = static::getBelongs();

			if(empty($belongs)){
				return false;
			}

			$logicBlocks = Logics::extractLogicBlocks($belongs);
			$logics = [];

			foreach ($logicBlocks as $logicBlock){
				$logics[] = $this->returnTrueOrFalseForALogicBlock($logicBlock, $belongsTo, $operator, $find);
			}

			return !in_array(false, $logics );

		} catch (\Exception $exception){
			return false;
		}
	}

	/**
	 * @param BelongModel[] $belongModels
	 * @param string $belongsTo
	 * @param string|null $operator
	 * @param null $find
	 *
	 * @return bool
	 */
	private function returnTrueOrFalseForALogicBlock(array $belongModels, string $belongsTo, ?string $operator = null, $find = null): bool
	{
		$matches = 0;

		foreach ($belongModels as $belongModel){

			if($belongModel->getBelongsTo() === $belongsTo){
				if($operator === null and $find == null){
					$matches++;
				} elseif($belongModel->getOperator() === $operator){
					switch ($operator){
						case Operator::EQUALS:
							if($find === $belongModel->getFind()){
								$matches++;
							}

							break;

						case Operator::NOT_EQUALS:
							if($find !== $belongModel->getFind()){
								$matches++;
							}

							break;

						case Operator::IN:
                            if(is_string($find) and is_string($belongModel->getFind())){
                                $check = Strings::matches($find, $belongModel->getFind());

                                if(count($check) > 0){
                                    $matches++;
                                }
                            }

							break;

						case Operator::NOT_IN:
                            if(is_string($find) and is_string($belongModel->getFind())){
                                $check = Strings::matches($find, $belongModel->getFind());

                                if( empty($check)){
                                    $matches++;
                                }
                            }

                            break;
					}
				}

				switch ($operator){
					case Operator::EQUALS:

						if( $belongModel->getOperator() === Operator::NOT_EQUALS){
							if($find !== $belongModel->getFind()){
								$matches++;
							}
						} elseif( $belongModel->getOperator() === Operator::IN){
						    if(is_string($find) and is_string($belongModel->getFind())){
                                $check = Strings::matches($find, $belongModel->getFind());

                                if(count($check) > 0){
                                    $matches++;
                                }
                            }
						} elseif( $belongModel->getOperator() === Operator::NOT_IN){
                            if(is_string($find) and is_string($belongModel->getFind())){
                                $check = Strings::matches($find, $belongModel->getFind());

                                if(empty($check)){
                                    $matches++;
                                }
                            }
						} elseif($belongModel->getFind() === $find){
							$matches++;
						}

						break;

					case Operator::NOT_EQUALS:

						if($belongModel->getFind() !== $find){
							$matches++;
						}

						break;

					case Operator::IN:
                        if(is_string($find) and is_string($belongModel->getFind())){
                            $check = Strings::matches($find, $belongModel->getFind());

                            if( $belongModel->getOperator() === Operator::NOT_IN){
                                if(empty($check)){
                                    $matches++;
                                }
                            } elseif(count($check) > 0){
                                $matches++;
                            }
                        }

						break;

					case Operator::NOT_IN:
                        if(is_string($find) and is_string($belongModel->getFind())){
                            $check = Strings::matches($find, $belongModel->getFind());

                            if(empty($check)){
                                $matches++;
                            }
                        }

                        break;

				}
			}
		}

		return $matches > 0;
	}

    /**
     * @return bool
     */
	public function belongsToTaxonomy()
    {
        $belongs = [
            MetaTypes::TAXONOMY,
            BelongsTo::TERM_ID,
        ];

        foreach ($belongs as $belong){
            if($this->belongsTo($belong)){
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function belongsToPostType()
    {
        $belongs = [
            MetaTypes::CUSTOM_POST_TYPE,
            BelongsTo::PARENT_POST_ID,
            BelongsTo::POST_ID,
            BelongsTo::POST_CAT,
            BelongsTo::POST_TAX,
            BelongsTo::POST_TEMPLATE,
        ];

        foreach ($belongs as $belong){
            if($this->belongsTo($belong)){
                return true;
            }
        }

        return false;
    }
}