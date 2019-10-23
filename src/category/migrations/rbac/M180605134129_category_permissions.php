<?php

namespace ant\category\migrations\rbac;

use yii\db\Schema;
use common\rbac\Migration;
use common\rbac\Permission;
use common\rbac\Role;
use ant\subscription\models\Subscription;

class M180605134129_category_permissions extends Migration
{
	protected $permissions;

	public function init() {
		$this->permissions = [
			\backend\modules\category\controllers\DefaultController::className() => [
				'index' => ['View list of categories', [Role::ROLE_ADMIN]],
				'view' => ['View category', [Role::ROLE_ADMIN]],
				'update' => ['Update category', [Role::ROLE_ADMIN]],
				'delete' => ['Delete category', [Role::ROLE_ADMIN]],
				'create' => ['Create a new category', [Role::ROLE_ADMIN]],
				'image-upload' => ['Upload image for category', [Role::ROLE_ADMIN]],
				'image-delete' => ['Delete image for category', [Role::ROLE_ADMIN]],
			],
			\backend\modules\category\controllers\CategoryController::className() => [
				'ajax-list' => ['Ajax get category list', [Role::ROLE_ADMIN]],
				'move-tree-node' => ['Move tree node in category list', [Role::ROLE_ADMIN]],
			],
			\frontend\modules\category\controllers\CategoryController::className() => [
				'index' => ['View main ategory page', [Role::ROLE_GUEST]],
				'view' => ['View category detail page', [Role::ROLE_GUEST]],
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
