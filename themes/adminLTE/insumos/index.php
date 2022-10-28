<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TipoMedida;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ConfiguracionpensionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Insumos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="insumos-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_insumos',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'codigo_insumo',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
           
            [
                'attribute' => 'descripcion',
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [
                'attribute' => 'id_tipo_medida',
                'value' => function($model){
                   $tipo = TipoMedida::findOne($model->id_tipo_medida);
                   return $tipo->medida;
                },
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
             [
                'attribute' => 'precio_unitario',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
             
            [
                'attribute' => 'estado_insumo',
                'value' => function($model){
                    $insumos = app\models\Insumos::findOne($model->id_insumos);                    
                    return $insumos->estado;
                },
                'filter' => ArrayHelper::map(app\models\Insumos::find()->all(),'estado_insumo','estado'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'fecha_entrada',
                'contentOptions' => ['class' => 'col-lg-1.1'],
            ],    
            [
                'attribute' => 'usuariosistema',
                'contentOptions' => ['class' => 'col-lg-1'],
            ], 
            
            [
                'class' => 'yii\grid\ActionColumn',
                  'contentOptions' => ['class' => 'col-lg-1'],
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: <span class="badge"> {totalCount}</span></div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]);
    ?>
</div>