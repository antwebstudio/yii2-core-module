<?php

namespace ant\support\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "em_contact_form".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property integer $age
 * @property string $mobile
 * @property string $email
 * @property string $country
 * @property string $create_ip
 * @property string $created_at
 * @property string $updated_at
 */
class ContactForm extends \yii\db\ActiveRecord
{
	const SCENARIO_NAME_AND_CONTACT = 'name_and_contact';
	
	public $tnc;

	protected $_fields = [
		'title' => [
			'rules' => [
                [['title'], 'required'],
			],
		],
		'age' => [
			'rules' => [
                [['age'], 'required'],
			],
		],
		'mobile' => [
			'rules' => [
                [['mobile'], 'required'],
			],
		],
		'address' => [
			'rules' => [
                [['address'], 'required'],
			],
		],
		'state' => [
			'rules' => [
                [['state'], 'required'],
			],
		],
	];

	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contact_form}}';
    }

	public function behaviors()
    {
        return
        [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
			[
				'class' => \ant\behaviors\AttachBehaviorBehavior::className(),
                'config' => '@common/config/behaviors.php',
			],
			[
				'class' => \ant\behaviors\SerializeBehavior::className(),
				'serializeMethod' => \ant\behaviors\SerializeBehavior::METHOD_JSON,
				'attributes' => ['data'],
			],
			[
				'class' => \ant\behaviors\SendEmailBehavior::className(),
				'template' => [
					self::EVENT_AFTER_INSERT => '@common/modules/support/mail/contactRegister',
				],
				'messageConfig' => [
					self::EVENT_AFTER_INSERT => [
						'to' => env('ADMIN_EMAIL'),
						'from' => env('ROBOT_EMAIL'),
						'subject' => 'Contact Registration Form',
					],
				],
				//'throwException' => YII_DEBUG,
			],
            [
                'class' => \ant\behaviors\TimestampBehavior::className(),
            ],
            [
                'class' => \ant\behaviors\IpBehavior::className(),
				'updatedIpAttribute' => null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
			//[['name', 'email', 'message'], 'required'],
			[['name', 'mobile'], 'required', 'on' => self::SCENARIO_NAME_AND_CONTACT],
			[['mobile'], 'udokmeci\yii2PhoneValidator\PhoneValidator', 'strict' => false, 'format' => false],
			[['email'], 'email'],
            //[['age'], 'integer'],
            //[['!created_ip'], 'required'],
			//[['tnc'], 'compare', 'operator' => '==', 'compareValue' => true, 'message' => 'Please check this checkbox to continue.'],
            [['message', 'data', 'created_at', 'updated_at'], 'safe'],
            [['name', 'title'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 20],
            [['state'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 200],
            [['country'], 'string', 'max' => 3],
            [['created_ip'], 'string', 'max' => 45],
        ];
		
		
		if ($this->hasMethod('getDynamicAttributeRules')) {
			return ArrayHelper::merge($rules, $this->getDynamicAttributeRules());
		} else {
			return $this->getCombinedRules($rules);
		}
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('contact', 'Name'),
            //'title' => Yii::t('contact', 'Title'),
            //'age' => Yii::t('contact', 'Age'),
            'mobile' => Yii::t('contact', 'Mobile'),
            'email' => Yii::t('contact', 'Email'),
            //'address' => Yii::t('contact', 'Address'),
            //'country' => Yii::t('contact', 'Country'),
            'created_ip' => 'Created Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
