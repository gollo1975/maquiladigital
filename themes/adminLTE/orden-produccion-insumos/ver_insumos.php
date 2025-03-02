<?php
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
                    Listado de insumos
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr style='font-size:85%;'>
                                <td scope="col" style='background-color:#B9D5CE'><b>Codigo</td>
                                <td scope="col" style='background-color:#B9D5CE'><b>Nombre del insumo</td>
                                <th scope="col" style='background-color:#B9D5CE;'><input type="checkbox" onclick="marcar(this);"/></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($model as $val): ?>
                                <tr style="font-size: 85%;">
                                    <td><?= $val->insumos->codigo_insumo ?></td>  
                                    <td><?= $val->insumos->descripcion ?></td>  
                                    <td><input type="checkbox" id="listado_insumos" name="listado_insumos[]" value="<?= $val->id_detalle ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>    
                    </table>
                   
                </div>
                <div class="panel-footer text-right">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-send'></span> Enviar", ["class" => "btn btn-success", 'name' => 'enviar_insumos_orden']) ?>
                </div>
                 
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
	function marcar(source) 
	{
		checkboxes=document.getElementsByTagName('input'); //obtenemos todos los controles del tipo Input
		for(i=0;i<checkboxes.length;i++) //recoremos todos los controles
		{
			if(checkboxes[i].type == "checkbox") //solo si es un checkbox entramos
			{
				checkboxes[i].checked=source.checked; //si es un checkbox le damos el valor del checkbox que lo llamó (Marcar/Desmarcar Todos)
			}
		}
	}
</script>
