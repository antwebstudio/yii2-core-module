<?php

namespace ant\file\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;
use ant\rbac\rules\IsOwnModelRule;
use ant\file\models\File;
use ant\file\controllers\FolderController;
use ant\file\controllers\FileController;
use ant\file\controllers\FileStorageItemController;

class M171113043400_file_permission extends Migration
{
	protected $permissions;
	
	
	public function init() {
		$this->permissions = [
			\ant\file\controllers\ElfinderController::className() => [
				'tinymce' => ['Manage file', [Role::ROLE_ADMIN]],
				'connector' => ['Manage file', [Role::ROLE_ADMIN]],
			],
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
