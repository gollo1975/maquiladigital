<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\PeriodoPago;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PeriodopagoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Periodo de pagos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="periodo-pago-index">

     <?= $this->render('_search', ['model' => $searchModel]); ?>
     <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_periodo_pago',
                'contentOptions' => ['class' => 'col-lg-1.2'],
            ],
            [
                'attribute' => 'nombre_periodo',
                'contentOptions' => ['class' => 'col-lg-3.6'],
            ],
            [
                'attribute' => 'dias',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'limite_horas',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'continua',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'periodo_mes',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: <span class="badge"> {totalCount}</span>  </div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]); ?>
</div>
