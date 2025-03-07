<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ResolucionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resoluciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resoluciones-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          
            [
                'attribute' => 'nroresolucion',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'id_documento',
                'value' => function($model){
                    $documento = app\models\DocumentoElectronico::findOne($model->id_documento);
                    return $documento->nombre_documento;
                },
                'filter' => ArrayHelper::map(app\models\DocumentoElectronico::find()->all(),'id_documento','nombre_documento'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
                    
            [
                'attribute' => 'inicio_rango',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'final_rango',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            [               
            'attribute' => 'fechacreacion',
            'value' => function($model){
                $resolucion = \app\models\Resolucion::findOne($model->idresolucion);
                return date("Y-m-d", strtotime("$resolucion->fechacreacion"));
            },
            'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [               
            'attribute' => 'fechavencimiento',
            'value' => function($model){
                $resolucion = \app\models\Resolucion::findOne($model->idresolucion);
                return date("Y-m-d", strtotime("$resolucion->fechavencimiento"));
            },
            'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'vigencia',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            [               
                'attribute' => 'activo',
                'value' => function($model){
                    $resolucion = \app\models\Resolucion::findOne($model->idresolucion);
                    if ($resolucion->activo == 0){$estado = "SI";}else{$estado = "NO";}
                    return $estado;
                },
                'contentOptions' => ['class' => 'col-lg-1'],
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
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>'
        ],
    ]);
    ?>
</div>