<?php

namespace ACPT\Tests;

use ACPT\Constants\MetaTypes;
use ACPT\Core\Models\Meta\MetaFieldModel;

class QRCodeFieldValuesTest extends AbstractTestCase
{
	/**
	 * @test
	 */
	public function can_add_acpt_meta_field_value()
	{
		$new_group = save_acpt_meta_group([
			'name' => 'new-group',
			'label' => 'New group',
			'belongs' => [
				[
					'belongsTo' => MetaTypes::CUSTOM_POST_TYPE,
					'operator'  => "=",
					"find"      => "page",
					"logic"     => "OR"
				],
                [
                        'belongsTo' => MetaTypes::TAXONOMY,
                        'operator'  => "=",
                        "find"      => "category",
                        "logic"     => "OR"
                ],
                [
                        'belongsTo' => MetaTypes::OPTION_PAGE,
                        'operator'  => "=",
                        "find"      => "new-page",
                        "logic"     => "OR"
                ],
                [
                        'belongsTo' => MetaTypes::USER,
                        'operator'  => "="
                ]
			],
			'boxes' => [
				[
					'name' => 'box_name',
					'label' => null,
					'fields' => [
						[
							'name' => 'qr_code_field',
							'label' => 'QR code field',
							'type' => MetaFieldModel::QR_CODE_TYPE,
							'showInArchive' => false,
							'isRequired' => false,
							'defaultValue' => "foo",
							'description' => "lorem ipsum dolor facium",
						]
					]
				],
			],
		]);

		$new_page = register_acpt_option_page([
			'menu_slug' => 'new-page',
			'page_title' => 'New page',
			'menu_title' => 'New page menu title',
			'icon' => 'admin-appearance',
			'capability' => 'manage_options',
			'description' => 'lorem ipsum',
			'position' => 77,
		]);

		$this->assertTrue($new_group);
		$this->assertTrue($new_page);

		foreach ($this->dataProvider() as $key => $value){
			$add_acpt_meta_field_value = save_acpt_meta_field_value([
				$key => $value,
				'box_name' => 'box_name',
				'field_name' => 'qr_code_field',
				'value' => "https://acpt.io",
			]);

			$this->assertTrue($add_acpt_meta_field_value);

			$acpt_field = get_acpt_field([
				$key => $value,
				'box_name' => 'box_name',
				'field_name' => 'qr_code_field',
			]);

            $this->assertEquals($acpt_field['url'], "https://acpt.io");
            $this->assertEquals($acpt_field['value']['resolution'], 200);
            $this->assertEquals($acpt_field['value']['colorLight'], "#ffffff");
            $this->assertEquals($acpt_field['value']['colorDark'], "#000000");
		}
	}

    /**
     * @depends can_add_acpt_meta_field_value
     * @test
     */
	public function can_edit_acpt_meta_field_value()
	{
		foreach ($this->dataProvider() as $key => $value){
			$edit_acpt_meta_field_value = save_acpt_meta_field_value([
				$key => $value,
				'box_name' => 'box_name',
				'field_name' => 'qr_code_field',
				'value' => "https://google.com",
			]);

			$this->assertTrue($edit_acpt_meta_field_value);

			$acpt_field = get_acpt_field([
				$key => $value,
				'box_name' => 'box_name',
				'field_name' => 'qr_code_field',
			]);

            $this->assertEquals($acpt_field['url'], "https://google.com");
            $this->assertEquals($acpt_field['value']['resolution'], 200);
            $this->assertEquals($acpt_field['value']['colorLight'], "#ffffff");
            $this->assertEquals($acpt_field['value']['colorDark'], "#000000");
		}
	}

    /**
     * @depends can_edit_acpt_meta_field_value
     * @test
     */
    public function can_delete_acpt_meta_field_value()
    {
	    foreach ($this->dataProvider() as $key => $value){
		    $delete_acpt_meta_field_value = delete_acpt_meta_field_value([
			    $key => $value,
			    'box_name' => 'box_name',
			    'field_name' => 'qr_code_field',
		    ]);

		    $this->assertTrue($delete_acpt_meta_field_value);
	    }

        $delete_acpt_meta_box = delete_acpt_meta_box('new-group', 'box_name');

        $this->assertTrue($delete_acpt_meta_box);

	    foreach ($this->dataProvider() as $key => $value){
		    $acpt_field = get_acpt_field([
			    $key => $value,
			    'box_name' => 'box_name',
			    'field_name' => 'qr_code_field',
		    ]);

		    $this->assertNull($acpt_field);
	    }

	    $delete_group = delete_acpt_meta_group('new-group');
	    $delete_acpt_option_page = delete_acpt_option_page('new-page', true);

	    $this->assertTrue($delete_group);
	    $this->assertTrue($delete_acpt_option_page);
    }
}