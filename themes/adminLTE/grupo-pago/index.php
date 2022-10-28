<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\GrupoPago;
use app\models\PeriodoPago;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrupoPagoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Grupos de Pago';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupo-pago-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_grupo_pago',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'grupo_pago',
                'contentOptions' => ['class' => 'col-lg-2.5'],
            ],
            [
                'attribute' => 'id_periodo_pago',
                'value' => function($model){
                   $nombrePeriodo = PeriodoPago::findOne($model->id_periodo_pago);
                   return $nombrePeriodo->nombre_periodo;
                },
                'filter' => ArrayHelper::map(PeriodoPago::find()->all(),'id_periodo_pago','nombre_periodo'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'ultimo_pago_nomina',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
                        [
                'attribute' => 'ultimo_pago_prima',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
                        [
                'attribute' => 'ultimo_pago_cesantia',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'estado',
                'value' => function($model) {
                    $estado = GrupoPago::findOne($model->id_grupo_pago);
                    return $estado->activo;
                },
                'filter' => ArrayHelper::map(GrupoPago::find()->all(), 'estado', 'activo'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: <span class="badge">{totalCount}</span></div>',
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