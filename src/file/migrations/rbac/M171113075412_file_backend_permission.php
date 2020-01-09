<?php

namespace ant\file\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M171113075412_file_backend_permission extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\file\backend\controllers\FileController::className() => [
				'delete' => ['Delete file from folder', [Role::ROLE_ADMIN]],
			],
			\ant\file\backend\controllers\FolderController::className() => [
				'index' => ['Manage folder', [Role::ROLE_ADMIN]],
				'upload' => ['Upload file to file storage', [Role::ROLE_ADMIN]],
				'delete' => ['Delete file from file storage', [Role::ROLE_ADMIN]],
			],
			\ant\file\backend\controllers\FileStorageItemController::className() => [
				'index' => ['Manage file', [Role::ROLE_ADMIN]],
				'delete' => ['Delete file', [Role::ROLE_ADMIN]],
				'upload' => ['Upload file', [Role::ROLE_USER]],
				'upload-delete' => ['Delete uploaded file', [Role::ROLE_USER]],
			],
			\ant\file\backend\controllers\FileAttachmentController::className() => [
				'index' => ['Manage file', [Role::ROLE_ADMIN]],
				'delete' => ['Delete file', [Role::ROLE_ADMIN]],
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
