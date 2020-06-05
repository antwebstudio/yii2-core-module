<?php

namespace ant\notification\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M200605052506Permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
            
			\ant\notification\backend\controllers\DefaultController::className() => [
				'index' => ['View own file', [Role::ROLE_ADMIN]],
				'test' => ['View own file', [Role::ROLE_ADMIN]],
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
