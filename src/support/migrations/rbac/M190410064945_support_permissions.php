<?php

namespace ant\support\migrations\rbac;

use yii\db\Schema;
use common\rbac\Migration;
use common\rbac\Role;

class M190410064945_support_permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\frontend\modules\support\controllers\ContactFormController::className() => [
				'create' => ['Send a enquiry to admin', [Role::ROLE_GUEST]],
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
