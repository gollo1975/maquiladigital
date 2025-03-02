<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdenproducciontipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de ordenes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ordenproducciontipo-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [                
                'attribute' => 'tipo',
                'contentOptions' => ['class' => 'col-lg-2'],                
            ],
            [
                'attribute' => 'activo',
                'value' => function($model){
                    $orden = app\models\Ordenproducciontipo::findOne($model->idtipo);                    
                    return $orden->estado;
                },
                'filter' => ArrayHelper::map(app\models\Ordenproducciontipo::find()->all(),'activo','estado'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'remision',
                'value' => function($model){
                    $orden = app\models\Ordenproducciontipo::findOne($model->idtipo);                    
                    return $orden->rremision;
                },
                'filter' => ArrayHelper::map(app\models\Ordenproducciontipo::find()->all(),'remision','rremision'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],    
            [
                'attribute' => 'ver_registro',
                'value' => function($model){
                    $orden = app\models\Ordenproducciontipo::findOne($model->idtipo);                    
                    return $orden->verregistro;
                },
                'filter' => ArrayHelper::map(app\models\Ordenproducciontipo::find()->all(),'ver_registro','verregistro'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],  
           [
                'attribute' => 'permite_insumos',
                'value' => function($model){
                    $orden = app\models\Ordenproducciontipo::findOne($model->idtipo);                    
                    return $orden->verinsumos;
                },
                'filter' => ArrayHelper::map(app\models\Ordenproducciontipo::find()->all(),'permite_insumos','verinsumos'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],                    
            [
                'class' => 'yii\grid\ActionColumn',    
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],
			
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],
        
    ]); ?>
</div>
