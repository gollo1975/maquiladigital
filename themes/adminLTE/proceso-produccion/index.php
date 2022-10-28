<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\ProcesoProduccion;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProcesoProduccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Procesos producción';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proceso-produccion-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'idproceso',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'proceso',
                'contentOptions' => ['class' => 'col-lg-5'],
            ],
            [
                'attribute' => 'segundos',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'minutos',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'estandarizado',
                'value' => function($model) {
                    $estandar = ProcesoProduccion::findOne($model->idproceso);
                    return $estandar->estandar;
                },
                'filter' => ArrayHelper::map(ProcesoProduccion::find()->all(), 'estandarizado', 'estandar'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
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
