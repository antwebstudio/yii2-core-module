<?php

namespace ant\file\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rule\IsOwnModelRule;
use ant\file\models\File;
use frontend\modules\file\controllers\FolderController;
use frontend\modules\file\controllers\FileController;
use frontend\modules\file\controllers\FileStorageItemController;

class M171113043400_file_permission extends Migration
{
	protected $permissions;
	
	
	public function init() {
		$this->permissions = [
			FolderController::className() => [
				'my-file' => ['View own file', [Role::ROLE_USER]],
			],
			FileController::className() => [
				'download' => ['Download own file', [Role::ROLE_USER]],
			],
			FileStorageItemController::className() => [
				'upload' => ['Upload file', [Role::ROLE_USER]],
				'upload-delete' => ['Delete uploaded file', [Role::ROLE_USER]],
			],
			File::className() => [
				'download' => ['Download own file', [Role::ROLE_USER], 'rule' => ['class' => IsOwnModelRule::className(), 'attribute' => 'ownerId']],
			],
		];
		
		parent::init();
	}
	
	public function up()
    {
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
