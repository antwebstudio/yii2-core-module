<?php
namespace ant\file\behaviors;

use yii\helpers\ArrayHelper;
use yii\validators\Validator;
use yii\db\ActiveRecord;
use ant\file\models\FileStorageItem;
use ant\file\models\FileAttachment;
use ant\file\models\FileAttachmentGroup;

class AttachmentBehavior extends \trntv\filekit\behaviors\UploadBehavior {
    public $attachments;
    public $attribute;

	public $linkModelExtraAttributes = [];
	public $modelType;
    public $uploadRelation;
    public $type = 'default';
	
    public $pathAttribute = 'path';
    public $baseUrlAttribute = 'base_url';
    public $orderAttribute = 'order';
    public $typeAttribute = 'type';
    public $sizeAttribute = 'size';
    public $nameAttribute = 'name';
	public $captionAttribute = 'caption';
	public $descriptionAttribute = 'description';
	public $dataAttribute = 'data';
    public $multiple = true;

    public $isRequired = false;

    protected $_validators;
	protected $_value;

    public function init() {
        parent::init();

        /*if (!$this->multiple) {
            throw new \Exception('Currently not support single attachment. When single attachment, the file attachment will no be saved.');
        }*/
    }
	
	/*protected function getDynamicAttributeValue($name) {
		return $this->_value[$name];
	}
	
	protected function setDynamicAttributeValue($name, $value) {
		return $this->_value[$name];
	}
	
	public function __call($name, $params) {
		$attribute = preg_replace('/^get(.*)/isU', '', $name);
		if ($this->isDynamicAttribute($attribute)) {
			return $this->getDynamicAttributeValue($attribute);
		}
		return parent::__call($name, $params);
	}

	public function hasMethod($name) {
		$attribute = preg_replace('/^get(.*)/isU', '', $name);
		return $this->isDynamicAttribute($attribute) || parent::hasMethod($name);
	}
  
	public function canGetProperty($name, $checkVars = true) {
		if ($this->isDynamicAttribute($name)) {
			return true;
		}
		return parent::canGetProperty($name, $checkVars);
	}
  
	public function canSetProperty($name, $checkVars = true) {
		if ($this->isDynamicAttribute($name)) {
			return true;
		}
		return parent::canSetProperty($name, $checkVars);
	}

	public function __set($name, $value) {
		if ($this->isDynamicAttribute($name)) {
			return $this->setDynamicAttributeValue($name, $value);
		}
		return parent::__set($name);
	}

	public function __get($name) {
		if ($this->isDynamicAttribute($name)) {
			return $this->getDynamicAttributeValue($name);
		}
		return parent::__get($name);
	}
  
	protected function isDynamicAttribute($attribute) {
		if (isset($this->attribute) && $attribute == $this->attribute) {
			return true;
		}
		return false;
	}*/

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFindMultiple',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsertMultiple',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdateMultiple',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDeleteMultiple',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }
	
	public function attach($owner) {
		parent::attach($owner);
        $this->linkModelExtraAttributes['model'] = $this->getModelType();
        
        if (!$owner->isAttributeSafe($this->attribute)) {
            throw new \Exception('For the AttachmentBehavior to work correctly, please set the attribute "'.$this->attribute.'" for the class "'.get_class($owner).'" to safe attribute. ');
        }

        $this->attachBehaviorRules($owner);
    }

    public function detach()
    {
        $this->detachBehaviorRules();
        return parent::detach();
    }

    public function behaviorRules() {
        // Currently not support client side validation, but server side validation work as expected.
        // Currently client side validation always return as not required.
        return [
            [[$this->attribute], 'required', 'when' => function($model, $attribute) {
                $isRequired = $this->isRequired;
                if (isset($this->isRequired) && is_callable($this->isRequired)) {
                    $isRequired = call_user_func_array($this->isRequired, []);
                }
                return $isRequired;
            }, 'whenClient' => 'function() {
                return false;
            }'],
        ];
    }

    protected function detachBehaviorRules() {
        $ownerValidators = $this->owner->validators;
        $cleanValidators = [];
        foreach ($ownerValidators as $validator) {
            if ( ! in_array($validator, $this->_validators)) {
                $cleanValidators[] = $validator;
            }
        }
        $ownerValidators->exchangeArray($cleanValidators);
    }

    protected function attachBehaviorRules($owner) {
        $rules = $this->behaviorRules();

        $validators = $owner->validators;
        foreach ($rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
                $this->_validators[] = $rule; // keep a reference in behavior
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $owner, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
                $this->_validators[] = $validator; // keep a reference in behavior
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
    }
	
	public function addAttachmentFromPath($files) {
		$modelClass = $this->getUploadModelClass();
        $group = $this->ensureAttachmentGroup();

        foreach ((array) $files as $file) {
			$attachment = FileAttachment::storeFromPath($file);
			//$attachment->group_id = $group->id;
			//if (!$attachment->save()) throw new \Exception('Failed to save. ');
			
            $group->link('attachments', $attachment);
        }
	}
    
    /*
    public function getFileAttachments() { 
		return $this->owner->hasMany(FileAttachment::className(), ['model_id' => 'id'])->onCondition(['model' => $this->getModelType()]);
	}*/

    public function getFileAttachments() {
        return $this->getAttachmentsRelation($this->type);
    }
	
	public function getFileAttachments2() {
		if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added before 2019-09-10
        return $this->getAttachmentsRelation($this->type);
    }

    public function getAttachmentsRelation($type) {
		return $this->owner->hasMany(FileAttachment::className(), ['group_id' => 'id'])
        ->via('fileAttachmentGroup', function($q) use ($type) { 
            $q->alias('group');
            return $q->onCondition(['type' => $type, 'model' => $this->getModelType()]);
        })//->onCondition(['group.type' => $type])
		->cache(7200, $this->getCacheDependency())
		->orderBy('order');
    }
	
	protected function getCacheTags() {
		return get_class($this->owner).'-'.$this->owner->id;
	}
	
	protected function getCacheDependency() {
		return new \yii\caching\TagDependency(['tags' => $this->getCacheTags()]);
	}
	
	protected function invalidateCache() {
		foreach (['cache', 'frontendCache', 'backendCache'] as $cacheComponent) {
			if (isset(\Yii::$app->{$cacheComponent})) {
				\yii\caching\TagDependency::invalidate(\Yii::$app->{$cacheComponent}, $this->getCacheTags());
			}
		}
	}

    protected function getUploadRelation()
    {
		if (isset($this->uploadRelation)) {
			$getter = 'get'.ucfirst($this->uploadRelation);
			return $this->owner->{$getter}();
		} else {
			return $this->getAttachmentsRelation($this->type);
		}
    }
    
    public function getFileAttachmentGroup($type = null) {
        return $this->owner->hasOne(FileAttachmentGroup::className(), ['model_id' => 'id'])
			->cache(7200, $this->getCacheDependency())
            ->onCondition(['model' => $this->getModelType(), 'type' => $type]);
    }
	
	protected function getModelType() {
		return \ant\models\ModelClass::getClassId($this->owner);
		return $this->modelType;
    }
	
    protected function ensureAttachmentGroup() {
        $group = $this->getFileAttachmentGroup($this->type)->one();
        if (!$group) {
            $group = new FileAttachmentGroup;
            $group->model = $this->getModelType();
            $group->type = $this->type;
            $this->owner->link('fileAttachmentGroup', $group, $this->linkModelExtraAttributes);
			
			$this->invalidateCache();
        }
        return $group;
    }

    /**
     * @param array $files
     */
    protected function saveFilesToRelation($files)
    {
        $modelClass = $this->getUploadModelClass();
        $group = $this->ensureAttachmentGroup();

        foreach ($files as $file) {
            $model = new $modelClass;
            $model->setScenario($this->uploadModelScenario);
			//$model->attributes = $this->linkModelExtraAttributes;
            $model = $this->loadModel($model, $file);
            if ($this->getUploadRelation()->via !== null) {
                $model->save(false);
            }
            $group->link('attachments', $model);
        }
		$this->invalidateCache();
    }

    /**
     * @param array $files
     */
    protected function updateFilesInRelation($files)
    {
        $modelClass = $this->getUploadModelClass();
        foreach ($files as $file) {
            $model = $modelClass::find()->cache(7200, $this->getCacheDependency())->joinWith('group group')
            ->andWhere(['group.type' => $this->type,
                'group.model' => $this->getModelType(),
                $this->getAttributeField('path') => $file['path']])
            ->one();
            
            if ($model) {
                $model->setScenario($this->uploadModelScenario);
				//$model->attributes = $this->linkModelExtraAttributes;
                $model = $this->loadModel($model, $file);
                $model->save(false);
            }
        }
		$this->invalidateCache();
    }

    public function afterUpdateMultiple()
    {
        $uploaded = $this->getUploaded();
        if (!$this->multiple) {
            $uploaded = [$uploaded];
        }

        $filesPaths = ArrayHelper::getColumn($uploaded, 'path');
        $models = $this->getUploadRelation()->all();
        $modelsPaths = ArrayHelper::getColumn($models, $this->getAttributeField('path'));
        $newFiles = $updatedFiles = [];
    
        foreach ($models as $model) {
            $path = $model->getAttribute($this->getAttributeField('path'));
            if (!in_array($path, $filesPaths, true) && $model->delete()) {
                $this->getStorage()->delete($path);
            }
        }
        foreach ($uploaded as $file) {
            if($file) {
                if (!in_array($file['path'], $modelsPaths, true)) {
                    $newFiles[] = $file;
                } else {
                    $updatedFiles[] = $file;
                }
            }
        }
        $this->saveFilesToRelation($newFiles);
        $this->updateFilesInRelation($updatedFiles);
    }

    public function fields()
    {
        $fields = [
            $this->attributePathName ? : 'path' => $this->pathAttribute,
            $this->attributeBaseUrlName ? : 'base_url' => $this->baseUrlAttribute,
            'type' => $this->typeAttribute,
            'size' => $this->sizeAttribute,
            'name' => $this->nameAttribute,
            'order' => $this->orderAttribute,
            'caption' => $this->captionAttribute,
            'description' => $this->descriptionAttribute,
			'data' => $this->dataAttribute,
        ];

        if ($this->attributePrefix !== null) {
            $fields = array_map(function ($fieldName) {
                return $this->attributePrefix . $fieldName;
            }, $fields);
        }

        return $fields;
    }
    
    public function afterFindMultiple()
    {
        $models = $this->getUploadRelation()->all();
        
        $fields = $this->fields();
        $data = [];
        foreach ($models as $k => $model) {
            /* @var $model \yii\db\BaseActiveRecord */
            $file = [];
            foreach ($fields as $dataField => $modelAttribute) {
                $file[$dataField] = $model->hasAttribute($modelAttribute)
                    ? ArrayHelper::getValue($model, $modelAttribute)
                    : null;
            }
            if ($file['path']) {
                $data[$k] = $this->enrichFileData($file);
            }
        }

        if (!$this->multiple) {
            $data = isset($data) && isset($data[0]) ? $data[0] : [];
        }
        $this->owner->{$this->attribute} = $data;
        // echo '<pre>';
        // print_r($this->attribute);
        // echo '<br/>';
        // print_r($this->owner->{$this->attribute});
        // die;
    }

    public function afterInsertMultiple()
    {
        if ($this->owner->{$this->attribute}) {
            if (is_array($this->owner->{$this->attribute}) && !isset($this->owner->{$this->attribute}[0]) ) {
                $data = [$this->owner->{$this->attribute}];
            } else {
                $data = $this->owner->{$this->attribute};
            }
            $this->saveFilesToRelation($data);
        }
    }
}                         