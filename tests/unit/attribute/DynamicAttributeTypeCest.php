<?php 
namespace attribute;

use UnitTester;

class DynamicAttributeTypeCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testSetDynamicAttributeSettings(UnitTester $I)
    {
		$label = 'field label';
		$model = new DynamicAttributeTypeCestTestModel;
		$model->attributes = [
			'dynamicAttributeSettings' => [
				'new1' => [
					'label' => $label,
					'class' => \ant\dynamicform\fieldtypes\classes\TextField::class,
				],
			],
		];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = DynamicAttributeTypeCestTestModel::findOne($model->id);
		
		//throw new \Exception($I->renderDbTable('{{%model_class}}').$I->renderDbTable('{{%test}}').$I->renderDbTable('{{%dynamic_form}}').$I->renderDbTable('{{%dynamic_form_field}}').$I->renderDbTable('{{%dynamic_form_field_map}}'));
		
		$I->assertTrue(isset($model->dynamicForm));
		$I->assertEquals(1, count($model->dynamicFields));
		$I->assertEquals($label, current($model->dynamicFields)->label);
    }
	
    public function testGetDynamicAttributeSettings(UnitTester $I)
    {
		$label = 'field label';
		$class = \ant\dynamicform\fieldtypes\classes\TextField::class;
		$settings = [
			'new1' => [
				'label' => $label,
				'class' => $class,
				'required' => 1,
				'setting' => [
					'min' => 1,
					'max' => 10,
				],
			],
		];
		
		$model = new DynamicAttributeTypeCestTestModel;
		$model->attributes = [
			'dynamicAttributeSettings' => $settings,
		];
		if (!$model->save()) throw new \Exception(print_r($model->errors, 1));
		
		$model = DynamicAttributeTypeCestTestModel::findOne($model->id);
		
		//throw new \Exception($I->renderDbTable('{{%model_class}}').$I->renderDbTable('{{%test}}').$I->renderDbTable('{{%dynamic_form}}').$I->renderDbTable('{{%dynamic_form_field}}').$I->renderDbTable('{{%dynamic_form_field_map}}'));
		
		$I->assertTrue(isset($model->dynamicAttributeSettings));
		$I->assertEquals(1, count($model->dynamicAttributeSettings));
		
		$fieldSetting = current($model->dynamicAttributeSettings);
		
		$I->assertEquals($settings['new1']['label'], $fieldSetting['label']);
		$I->assertEquals($settings['new1']['class'], $fieldSetting['class']);
		$I->assertEquals($settings['new1']['required'], $fieldSetting['required']);
		$I->assertEquals($settings['new1']['setting'], $fieldSetting['setting']);
    }
}

class DynamicAttributeTypeCestTestModel extends \yii\db\ActiveRecord {
	public function rules() {
		return [
			[['dynamicAttributeSettings'], 'safe'],
		];
	}
	
	public function behaviors() {
		return [
            [
                'class' => \ant\attribute\behaviors\DynamicAttributeType::class,
            ],
		];
	}
	
	public static function tableName() {
		return '{{%test}}';
	}
}
