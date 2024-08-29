<?php
use yii\helpers\Html;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\TallaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo de productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-novedad-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'id_tipo_producto',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'concepto',
                'contentOptions' => ['class' => 'col-lg-4 '],
            ],
            [               
                'attribute' => 'estado',
                'value' => function($model){
                    $tipo = \app\models\TipoProducto::findOne($model->id_tipo_producto);
                    if ($tipo->estado == 0){$estado = "SI";}else{$estado = "NO";}
                    return $estado;
                },
                'contentOptions' => ['class' => 'col-lg-2'],
            ], 		
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],

        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros <spam class="badge"> {totalCount}</spam>  </div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],

    ]); ?>
</div>