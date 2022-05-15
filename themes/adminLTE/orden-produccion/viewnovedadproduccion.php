 <?php

//modelos

//clase
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\Session;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\AccessControl;


/* @var $this yii\web\View */
/* @var $model app\models\Ordenproduccion */

$this->title = 'Vista novedad';
$this->params['breadcrumbs'][] = $model->idordenproduccion;
?>
<div class="ordenproduccion-viewnovedad">
  <?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-8 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-2 control-label'],
        'options' => []
    ],
    ]);?>
        <?= Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span> Regresar', ['view', 'id' => $id], ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if ($model->autorizado == 0) { ?>
                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['editarnovedadproduccion', 'id' => $id,'id_novedad' => $id_novedad], ['class' => 'btn btn-success btn-sm']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Eliminar', ['eliminarnovedadproduccion', 'id' => $id, 'id_novedad' => $id_novedad], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Esta seguro de eliminar el registro?',
                            'method' => 'post' ,
                        ],
                    ]) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-ok"></span> Autorizar', ['autorizadonovedad', 'id' => $id, 'id_novedad' => $id_novedad], ['class' => 'btn btn-default btn-sm']);
        }else {
                echo Html::a('<span class="glyphicon glyphicon-remove"></span> Desautorizar', ['autorizadonovedad', 'id' => $id, 'id_novedad' => $id_novedad], ['class' => 'btn btn-default btn-sm']);
                echo Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', ['imprimirnovedadorden', 'id' => $id,'id_novedad' => $id_novedad], ['class' => 'btn btn-default btn-sm']);?>            
        <?php }    ?>     
  

    <br>
    <br>    
    <div class="panel panel-success">
        <div class="panel-heading">
            Orden de Producción
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
                <tr style="font-size: 85%;">
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, "No_Novedad") ?>:</th>
                    <td><?= Html::encode($model->id_novedad) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Orden_Producción') ?>:</th>
                    <td><?= Html::encode($model->idordenproduccion) ?></td>
                    <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Fecha_Proceso') ?>:</th>
                    <td><?= Html::encode($model->fecha_proceso) ?></td>
                     <th style='background-color:#F0F3EF;'><?= Html::activeLabel($model, 'Usuario') ?>:</th>
                    <td><?= Html::encode($model->usuariosistema) ?></td>
                </tr>
                <tr style="font-size: 85%;">
                        <?= $form->field($model, 'novedad', ['template' => '{label}<div class="col-sm-10 form-group">{input}{error}</div>'])->textarea(['rows' => 10]) ?>
                </tr>
            </table>
        </div>
    </div>
   <?php ActiveForm::end(); ?>  
</div>

   