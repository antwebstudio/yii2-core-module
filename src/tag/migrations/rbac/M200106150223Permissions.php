<?php

namespace ant\tag\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M200106150223Permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\tag\controllers\TagController::className() => [
				'view' => ['View tag', [Role::ROLE_GUEST]],
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
