<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'My Yii Application';

$columns = [
	['class' => 'yii\grid\SerialColumn'],
	[
		'attribute' => 'name',
		'content' => function ($model) {
			/** @var \app\models\Product $model */
			
			$rev = ArrayHelper::map($model->revisions, 'attr', 'new_value');
			
			if (!Yii::$app->user->identity || (Yii::$app->user->identity && Yii::$app->user->identity->role_id == 3)) {
				return $model->name;
			} else {
				if ($rev['name'] && $model->name) {
					return '<del>' . $model->name . '</del> | <strong>' . $rev['name'] . '</strong>';
				} elseif (!$rev['name'] && $model->name) {
					return $model->name;
                } else {
					return '<strong>New product</strong>: ' . $rev['name'];
				}
			}
		}
	
	],
	[
		'attribute' => 'price',
		'content' => function ($model) {
			/** @var \app\models\Product $model */
			
			$rev = ArrayHelper::map($model->revisions, 'attr', 'new_value');
			
			if (!Yii::$app->user->identity || (Yii::$app->user->identity && Yii::$app->user->identity->role_id == 3)) {
				return $model->price;
			} else {
				if ($rev['price'] && $model->price) {
					return '<del>' . $model->price . '</del> | <strong>' . $rev['price'] . '</strong>';
				} elseif (!$rev['price'] && $model->price) {
					return $model->price;
				} else {
					return '<strong>New product</strong>: ' . $rev['price'];
				}
			}
		}
	]
];

if (Yii::$app->user->identity && Yii::$app->user->identity->role_id == 2) {
    $columns[] = [
        'label' => 'Moderate',
	    'content' => function ($model) {
		    /** @var \app\models\Product $model */
		
		    $rev = ArrayHelper::map($model->revisions, 'attr', 'new_value');
      
		    if ($rev['name'] != null || $rev['price'] != null) {
		        return Html::a('Approve', \yii\helpers\Url::to(['approve', 'id' => $model->id]));
            } else {
		        return '<span class="label label-success">Approved</span>';
            }
	    }
    ];
}

if (Yii::$app->user->identity && Yii::$app->user->identity->role_id == 1) {
	$columns[] = [
		'label' => '',
		'content' => function ($model) {
			/** @var \app\models\Product $model */
			
			return Html::a('Update', \yii\helpers\Url::to(['update', 'id' => $model->id])) . ' | ' .
			       Html::a('Delete', \yii\helpers\Url::to(['delete', 'id' => $model->id]));
		}
	];
}

?>
<div class="site-index">
    
    <div class="body-content">

        <?= Html::a('Add product', \yii\helpers\Url::to(['create']), ['class' => 'btn btn-primary'])?>

        <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        //'filterModel' => $searchModel,
	        'columns' => $columns
        ]); ?>
        
    </div>
</div>
