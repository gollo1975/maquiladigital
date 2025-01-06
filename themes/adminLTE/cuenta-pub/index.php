<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\CuentaPub;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CuentaPubSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cuentas Pub';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cuenta-pub-index">

<?php
//use kartik\export\ExportMenu;
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'codigo_cuenta',
    'nombre_cuenta',    
    [
        'attribute' => 'permite_movimientos',
        'value' => function($model){
            $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
            return $cuenta->permitem;
        },
        'filter' => ArrayHelper::map(CuentaPub::find()->all(),'permite_movimientos','permitem'),
        'contentOptions' => ['class' => 'col-lg-2'],
    ],
    [
        'attribute' => 'exige_nit',
        'value' => function($model){
            $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
            return $cuenta->exigen;
        },
        'filter' => ArrayHelper::map(CuentaPub::find()->all(),'exige_nit','exigen'),
        'contentOptions' => ['class' => 'col-lg-2'],
    ],
    [
        'attribute' => 'exige_centro_costo',
        'value' => function($model){
            $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
            return $cuenta->exigecc;
        },
        'filter' => ArrayHelper::map(CuentaPub::find()->all(),'exige_centro_costo','exigecc'),
        'contentOptions' => ['class' => 'col-lg-2'],
    ],
                
    ['class' => 'yii\grid\ActionColumn'],
];


?>
    <!-- index -->
    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'codigo_cuenta',
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [                
                'attribute' => 'nombre_cuenta',
                'contentOptions' => ['class' => 'col-lg-3'],                
            ],
            [
                'attribute' => 'permite_movimientos',
                'value' => function($model){
                    $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
                    return $cuenta->permitem;
                },
                'filter' => ArrayHelper::map(CuentaPub::find()->all(),'permite_movimientos','permitem'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'exige_nit',
                'value' => function($model){
                    $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
                    return $cuenta->exigen;
                },
                'filter' => ArrayHelper::map(CuentaPub::find()->all(),'exige_nit','exigen'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'exige_centro_costo',
                'value' => function($model){
                    $cuenta = CuentaPub::findOne($model->codigo_cuenta);                    
                    return $cuenta->exigecc;
                },
                'filter' => ArrayHelper::map(CuentaPub::find()->all(),'exige_centro_costo','exigecc'),
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


