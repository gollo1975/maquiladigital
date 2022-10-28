<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\CajaCompensacion;
use yii\helpers\ArrayHelper;
use app\models\Municipio;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CajaCompensacionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Cajas de Compensacion';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="caja-compensacion-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_caja_compensacion',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'caja',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'telefono',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'direccion',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'codigo_caja',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'codigo_interfaz',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'idmunicipio',
                'value' => function($model) {
                    $caja = CajaCompensacion::findOne($model->id_caja_compensacion);
                    $municipio = Municipio::findOne($caja->idmunicipio);
                    return $municipio->municipio;
                },
                'filter' => ArrayHelper::map(CajaCompensacion::find()->all(), 'idmunicipio', 'municipios'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'estado',
                'value' => function($model) {
                    $orden = CajaCompensacion::findOne($model->id_caja_compensacion);
                    return $orden->activo;
                },
                'filter' => ArrayHelper::map(CajaCompensacion::find()->all(), 'estado', 'activo'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',
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