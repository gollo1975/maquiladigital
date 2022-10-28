<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\TiempoServicio;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipoReciboSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tiempo de servicio';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiempo-servicio-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_tiempo',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'tiempo_servicio',
                'contentOptions' => ['class' => 'col-lg-4'],
            ],
            [
                'attribute' => 'horas_dia',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'pago_incapacidad_general',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'pago_incapacidad_laboral',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1 '],
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