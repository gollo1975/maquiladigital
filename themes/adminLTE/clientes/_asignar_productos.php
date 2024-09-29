<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Ordenproduccion;
use app\models\Ordenproducciondetalle;
?>


<?php $form = ActiveForm::begin([

    'options' => ['class' => 'form-horizontal condensed', 'role' => 'form'],
    'fieldConfig' => [
        'template' => '{label}<div class="col-sm-5 form-group">{input}{error}</div>',
        'labelOptions' => ['class' => 'col-sm-3 control-label'],
        'options' => []
    ],
]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        
        <div class="table table-responsive">
            <div class="panel panel-success ">
                <div class="panel-heading">
                    Lineas <span class="badge"><?= count($productos)?></span>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive-lg" >
                        <thead>
                            <tr style='font-size:100%;'>
                                <td scope="col" style='background-color:#B9D5CE; '><b>Linea de confeccion</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Valor confeccion</td>
                                <td scope="col" style='background-color:#B9D5CE;'><b>Valor Terminacion</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($productos as $val):
                                $Search = \app\models\ClientePrendas::find()->where(['=','id_cliente', $id])->andWhere(['=','id_tipo_producto', $val->id_tipo_producto])->one();
                                if(!$Search){ ?>
                                    <tr>
                                        <td><?= $val->concepto ?></td> 
                                        <td style="padding-left: 1;padding-right: 1;"><input type="text" style= "text-align: right" name="valor_confeccion[]" value="" size="8" ></td>
                                        <td style="padding-left: 1;padding-right: 1;"><input type="text" style= "text-align: right" name="valor_terminacion[]" value="" size="8"></td>
                                        <input type="hidden" name="configuracion_productos[]" value="<?= $val->id_tipo_producto ?>">
                                    </tr>
                                <?php }?>
                            <?php
                            endforeach; ?>    
                        </tbody>
                    </table>
                    <div class="panel-footer text-right">			
                    <?= Html::submitButton("<span class='glyphicon glyphicon-floppy-disk'></span> Enviar", ["class" => "btn btn-primary", 'name' => 'enviar_productos']) ?>                    
                   </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
