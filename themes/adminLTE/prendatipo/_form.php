<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\app\models\Talla;

/* @var $this yii\web\View */
/* @var $model app\models\Prendatipo */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

<div class="panel panel-success">
    <div class="panel-heading">
        <h4>Información de la prenda</h4>
    </div>
    <div class="panel-body">

        <div class="row">
            <?= $form->field($model, 'prenda')->textInput(['maxlength' => true]) ?>
        </div>  
        <div class="panel-footer text-right">            
            <a href="<?= Url::toRoute("prendatipo/index") ?>" class="btn btn-primary"><span class='glyphicon glyphicon-circle-arrow-left'></span> Regresar</a>
            <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Guardar", ["class" => "btn btn-success",]) ?>
        </div>
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#listadotallasmujer" aria-controls="listadotallasmujer" role="tab" data-toggle="tab">Listado de tallas (Dama) <span class="badge"><?= count($tallasM) ?></span></a></li>
                <li role="presentation" ><a href="#listadotallashombre" aria-controls="listadotallashombre" role="tab" data-toggle="tab">Listado de tallas (Hombre) <span class="badge"><?= count($tallasH) ?></span></a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="listadotallasmujer">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'> Nombe de la talla</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($tallasM as $talla) {?>
                                            <tr>    
                                                <td>
                                                    <input type="checkbox" name="seleccion_tallas[]" value="<?= $talla->idtalla ?>">
                                                    Talla (<?= $talla->talla?>)-(<?= $talla->sexo?>)
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>    
                            </div>
                        </div>
                    </div>
                </div><!--<!-- TERMINA TABS -->
                
                <div role="tabpanel" class="tab-pane " id="listadotallashombre">
                    <div class="table-responsive">
                        <div class="panel panel-success">
                            <div class="panel-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col" style='background-color:#B9D5CE;'> Nombe de la talla</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($tallasH as $talla) {?>
                                            <tr>    
                                                <td>
                                                    <input type="checkbox" name="seleccion_tallas[]" value="<?= $talla->idtalla ?>">
                                                    Talla (<?= $talla->talla?>)-(<?= $talla->sexo?>)
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>    
                            </div>
                        </div>
                    </div>
                </div><!--<!-- TERMINA TABS -->
            </div>    
        </div><!--<!-- TERMINA EL TABS -->    
     </div>
    </div>
<?php ActiveForm::end(); ?>


