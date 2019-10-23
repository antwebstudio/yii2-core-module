<?php

namespace ant\file\migrations\rbac;

use yii\db\Schema;
use common\rbac\Migration;
use common\rbac\Role;
use backend\modules\file\controllers\FolderController;
use backend\modules\file\controllers\FileController;

class M171113075412_file_backend_permission extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			FileController::className() => [
				'delete' => ['Delete file from folder', [Role::ROLE_ADMIN]],
			],
			FolderController::className() => [
				'index' => ['Manage folder', [Role::ROLE_ADMIN]],
				'upload' => ['Upload file to file storage', [Role::ROLE_ADMIN]],
				'delete' => ['Delete file from file storage', [Role::ROLE_ADMIN]],
			],
			\backend\modules\file\controllers\FileStorageItemController::className() => [
				'upload' => ['Upload file', [Role::ROLE_USER]],
				'upload-delete' => ['Delete uploaded file', [Role::ROLE_USER]],
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
