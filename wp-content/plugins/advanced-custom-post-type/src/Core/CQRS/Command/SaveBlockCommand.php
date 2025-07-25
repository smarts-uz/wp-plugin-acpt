<?php

namespace ACPT\Core\CQRS\Command;

use ACPT\Core\Helper\Uuid;
use ACPT\Core\Models\DynamicBlock\DynamicBlockControlModel;
use ACPT\Core\Models\DynamicBlock\DynamicBlockModel;
use ACPT\Core\Repository\DynamicBlockRepository;

class SaveBlockCommand implements CommandInterface
{
    /**
     * @var array
     */
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function execute()
    {
        $data = $this->data;

        if(empty($data)){
            return 0;
        }

        $blockModel = DynamicBlockModel::hydrateFromArray([
            'id' => $data['id'] ?? Uuid::v4(),
            'title' => $data['title'],
            'name' => $data['name'],
            'category' => $data['category'],
            'icon' => (is_array($data['icon']) ? [
                'src' => $data['icon']['src'],
                'background' => $data['icon']['background'],
                'foreground' => $data['icon']['foreground'],
            ] : $data['icon']) ,
            'css' => $data['css'] ?? null,
            'callback' => $data['callback'] ?? null,
            'keywords' => is_array($data['keywords']) ? $data['keywords'] : [],
            'postTypes' => is_array($data['postTypes']) ? $data['postTypes'] : [],
            'supports' => is_array($data['supports']) ? $data['supports'] : [],
        ]);

        if(isset($data['controls']) and !empty($data['controls'])){
            foreach ($data['controls'] as $index => $control){
                $controlModel = DynamicBlockControlModel::hydrateFromArray([
                    'id' => $control['id'] ?? Uuid::v4(),
                    'block' => $blockModel,
                    'name' => $control['name'],
                    'label' => $control['label'],
                    'type' => $control['type'],
                    'default' => $control['default'] ?? null,
                    'description' => $control['description'] ?? null,
                    'settings' => $control['settings'] ?? [],
                    'sort' => ($index+1),
                ]);

                if($control['options']){
                    $controlModel->setOptions($control['options']);
                }

                $blockModel->addControl($controlModel);
            }
        }

        DynamicBlockRepository::save($blockModel);

        return $blockModel->getId();
    }
}