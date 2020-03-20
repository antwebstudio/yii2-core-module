<?php

namespace ant\google\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M200320121214Permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\google\backend\controllers\DefaultController::className() => [
				'index' => ['Google api', [Role::ROLE_ADMIN]],
				'oauth' => ['Google api', [Role::ROLE_ADMIN]],
				'logout-oauth' => ['Google api', [Role::ROLE_ADMIN]],
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
