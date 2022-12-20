<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ComprobanteEgresoTipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de comprobantes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprobante-egreso-tipo-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'id_comprobante_egreso_tipo',
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [                
                'attribute' => 'concepto',
                'contentOptions' => ['class' => 'col-lg-4'],                
            ],
            [
                'attribute' => 'activo',
                'value' => function($model){
                    $tipo = app\models\ComprobanteEgresoTipo::findOne($model->id_comprobante_egreso_tipo);                    
                    return $tipo->estado;
                },
                'filter' => ArrayHelper::map(app\models\ComprobanteEgresoTipo::find()->all(),'activo','estado'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],     
                        [
                'attribute' => 'permite_importar',
                'value' => function($model){
                    $tipo = app\models\ComprobanteEgresoTipo::findOne($model->id_comprobante_egreso_tipo);                    
                    return $tipo->importar;
                },
                'filter' => ArrayHelper::map(app\models\ComprobanteEgresoTipo::find()->all(),'permite_importar','importar'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],           
            [
                    'class' => 'yii\grid\ActionColumn',              
                ],

            ],
            //'tableOptions' => ['class' => 'table table-success'],
            'tableOptions'=>['class'=>'table table-bordered table-success'],        
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


