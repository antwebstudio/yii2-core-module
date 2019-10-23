<?php
use yii\helpers\Html;
?>
<p>Name: <?= $model->name ?></p>
<p>Mobile: <?= $model->mobile ?></p>
<p>Email: <?= $model->email ?></p>
<p>Message: <?= nl2br($model->message) ?></p>