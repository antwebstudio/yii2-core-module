<?php

namespace ant\category\backend\controllers;

use Yii;
use SteelyWing\Chinese\Chinese;
use ant\category\models\Category;
use yii\db\Expression;
/*
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
*/
/*use common\modules\category\models\CategoryAttachment;
use common\modules\category\models\CategorySearch;
*/

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends \yii\web\Controller
{
    public function actionAjaxList($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = trim($q);

        if ($q == '') return [];

        $q = (new Chinese)->to(Chinese::CHS, $q);
        
        $result = Category::find()->select(['*', 'title AS text'])->andWhere(['like', 'title', $q])
            ->orderBy(new Expression('title = '.\Yii::$app->db->quoteValue($q).' DESC')) // To make sure the exact match will be the first option
            ->asArray()->all();
        
        if (count($result) == 0 || $result[0]['title'] != $q) {
            // Add the option to create the category if it is not exist.
            $result[] = ['id' => 'new:'.$q, 'text' => 'New Category: '.$q];
        }

        return $result;
    }
	
	public function actionMoveTreeNode($id, $parentId = null, $prevId = null) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$model = Category::findOne($id);
		
		if ($prevId) {
			// Move node to next to another node
			$prev = Category::findOne($prevId);
			if (!$model->insertAfter($prev)) throw new Exception('Failed to insert nested set node. ');
		} else if ($parentId) {
			// Move node as child of another node
			$parent = Category::findOne($parentId);
			if (!$model->prependTo($parent)) throw new Exception('Failed to append nested set node. ');
		} else {
			// Move node to root
			throw new \Exception('Not yet implemented');
			$root = Category::find()->andWhere(['id' => null])->one();
			if (!$model->moveAsFirst($root)) throw new Exception('Failed to move nested set node to root. ');	
		}
		return ['success' => true];
	}
}