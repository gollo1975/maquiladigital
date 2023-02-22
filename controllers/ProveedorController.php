<?php

namespace app\controllers;

use app\models\Ordenproduccion;
use Codeception\Lib\HelperModule;
use yii;
use yii\base\Model;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Proveedor;
use app\models\Municipio;
use app\models\Departamentos;
use app\models\FormProveedor;
use yii\helpers\Url;
use app\models\FormFiltroProveedor;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use app\models\UsuarioDetalle;


class ProveedorController extends Controller {

    public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',15])->all()){
                $form = new FormFiltroProveedor;
                $cedulanit = null;
                $nombrecorto = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $cedulanit = Html::encode($form->cedulanit);
                        $nombrecorto = Html::encode($form->nombrecorto);
                        $table = proveedor::find()
                                ->andFilterWhere(['like', 'cedulanit', $cedulanit])
                                ->andFilterWhere(['like', 'nombrecorto', $nombrecorto]);
                        $table = $table->orderBy('idproveedor desc');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                    ->all();
                        if(isset($_POST['excel'])){                    
                            $this->actionExcelconsulta($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Proveedor::find()
                            ->orderBy('idproveedor desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelconsulta($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }
    }

    public function actionNuevo() {
        $model = new FormProveedor();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $dv = Html::encode($_POST["dv"]);
            if ($model->validate()) {
                $table = new proveedor();
                $table->id_tipo_documento = $model->id_tipo_documento;
                $table->cedulanit = $model->cedulanit;
                $table->razonsocial = $model->razonsocial;
                $table->nombreproveedor = $model->nombreproveedor;
                $table->apellidoproveedor = $model->apellidoproveedor;
                $table->direccionproveedor = $model->direccionproveedor;
                $table->telefonoproveedor = $model->telefonoproveedor;
                $table->celularproveedor = $model->celularproveedor;
                $table->emailproveedor = $model->emailproveedor;
                $table->iddepartamento = $model->iddepartamento;
                $table->idmunicipio = $model->idmunicipio;
                $table->contacto = $model->contacto;
                $table->telefonocontacto = $model->telefonocontacto;
                $table->celularcontacto = $model->celularcontacto;
                $table->formapago = $model->formapago;
                $table->plazopago = $model->plazopago;
                $table->nitmatricula = $model->cedulanit;
                $table->tiporegimen = $model->tiporegimen;
                $table->autoretenedor = $model->autoretenedor;
                $table->naturaleza = $model->naturaleza;
                $table->sociedad = $model->sociedad;
                $table->observacion = $model->observacion;
                $table->dv = $dv;
                $table->banco = $model->banco;
                $table->tipocuenta = $model->tipocuenta;
                $table->cuentanumero = $model->cuentanumero;
                $table->genera_moda = $model->genera_moda;
                if ($model->id_tipo_documento == 1 || $model->id_tipo_documento == 2 || $model->id_tipo_documento == 9 || $model->id_tipo_documento == 4) {
                    $table->nombrecorto = $model->nombreproveedor . " " . $model->apellidoproveedor;
                    $model->razonsocial = null;
                } elseif ($model->id_tipo_documento == 5) {
                    $table->nombrecorto = $model->razonsocial;
                    $model->nombreproveedor = null;
                    $model->apellidoproveedor = null;
                }

                if ($table->insert()) {
                    return $this->redirect(['index']);
                } else {
                    $msg = "error";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('nuevo', ['model' => $model, 'msg' => $msg, 'tipomsg' => $tipomsg]);
    }

    public function actionEditar($id) {
        $model = new FormProveedor();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $dv = Html::encode($_POST["dv"]);
            if ($model->validate()) {
                $table = Proveedor::find()->where(['idproveedor' => $id])->one();
                if ($table) {
                    $table->id_tipo_documento = $model->id_tipo_documento;
                    $table->cedulanit = $model->cedulanit;
                    $table->razonsocial = $model->razonsocial;
                    $table->nombreproveedor = $model->nombreproveedor;
                    $table->apellidoproveedor = $model->apellidoproveedor;
                    $table->direccionproveedor = $model->direccionproveedor;
                    $table->telefonoproveedor = $model->telefonoproveedor;
                    $table->celularproveedor = $model->celularproveedor;
                    $table->emailproveedor = $model->emailproveedor;
                    $table->iddepartamento = $model->iddepartamento;
                    $table->idmunicipio = $model->idmunicipio;
                    $table->contacto = $model->contacto;
                    $table->telefonocontacto = $model->telefonocontacto;
                    $table->celularcontacto = $model->celularcontacto;
                    $table->formapago = $model->formapago;
                    $table->plazopago = $model->plazopago;
                    $table->nitmatricula = $model->cedulanit;
                    $table->tiporegimen = $model->tiporegimen;
                    $table->autoretenedor = $model->autoretenedor;
                    $table->sociedad = $model->sociedad;
                    $table->naturaleza = $model->naturaleza;
                    $table->observacion = $model->observacion;
                    $table->banco = $model->banco;
                    $table->tipocuenta = $model->tipocuenta;
                    $table->cuentanumero = $model->cuentanumero;
                    $table->genera_moda = $model->genera_moda;
                    if ($model->id_tipo_documento == 1 || $model->id_tipo_documento == 2 || $model->id_tipo_documento == 9  || $model->id_tipo_documento == 4) {
                        $table->nombrecorto = strtoupper($model->nombreproveedor . " " . $model->apellidoproveedor);
                        $model->razonsocial = null;
                    } elseif ($model->id_tipo_documento == 5) {
                        $table->nombrecorto = strtoupper($model->razonsocial);
                        $model->nombreproveedor = null;
                        $model->apellidoproveedor = null;
                    }
                    if ($table->update()) {
                        $msg = "El registro ha sido actualizado correctamente";
                        return $this->redirect(['index']);
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        $tipomsg = "danger";
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            } else {
                $model->getErrors();
            }
        }


        if (Yii::$app->request->get("id")) {
            $table = Proveedor::find()->where(['idproveedor' => $id])->one();
            $municipio = Municipio::find()->Where(['=', 'iddepartamento', $table->iddepartamento])->all();
            $municipio = ArrayHelper::map($municipio, "idmunicipio", "municipio");
            if ($table) {
                $model->id_tipo_documento = $table->id_tipo_documento;
                $model->cedulanit = $table->cedulanit;
                $model->razonsocial = $table->razonsocial;
                $model->nombreproveedor = $table->nombreproveedor;
                $model->apellidoproveedor = $table->apellidoproveedor;
                $model->direccionproveedor = $table->direccionproveedor;
                $model->telefonoproveedor = $table->telefonoproveedor;
                $model->celularproveedor = $table->celularproveedor;
                $model->emailproveedor = $table->emailproveedor;
                $model->iddepartamento = $table->iddepartamento;
                $model->idmunicipio = $table->idmunicipio;
                $model->contacto = $table->contacto;
                $model->telefonocontacto = $table->telefonocontacto;
                $model->celularcontacto = $table->celularcontacto;
                $model->formapago = $table->formapago;
                $model->plazopago = $table->plazopago;
                $model->nitmatricula = $table->nitmatricula;
                $model->tiporegimen = $table->tiporegimen;
                $model->autoretenedor = $table->autoretenedor;
                $model->naturaleza = $table->naturaleza;
                $model->sociedad = $table->sociedad;
                $model->dv = $table->dv;
                $model->observacion = $table->observacion;
                $model->banco = $table->banco;
                $model->tipocuenta = $table->tipocuenta;
                $model->cuentanumero = $table->cuentanumero;
                $model->genera_moda = $table->genera_moda;
            } else {
                return $this->redirect(["proveedor/index"]);
            }
        } else {
            return $this->redirect(["proveedor/index"]);
        }
        return $this->render("editar", ["model" => $model, "msg" => $msg, "tipomsg" => $tipomsg, "municipio" => $municipio]);
    }

    public function actionView($id) {
        // $model = new List();            
        $table = Proveedor::find()->where(['idproveedor' => $id])->one();
        return $this->render('view', ['table' => $table
        ]);
    }

    public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $proveedor = Proveedor::findOne($id);
            if ((int) $id) {
                try {
                    proveedor::deleteAll("idproveedor=:idproveedor", [":idproveedor" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                    $this->redirect(["proveedor/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["proveedor/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el proveedor ' . $proveedor->cedulanit - $proveedor->nombrecorto . ' tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["proveedor/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el proveedor ' . $proveedor->cedulanit . '-' . $proveedor->nombrecorto . ' tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el proveedor, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("proveedor/index") . "'>";
            }
        } else {
            return $this->redirect(["proveedor/index"]);
        }
    }

    public function actionMunicipio($id) {
        $rows = Municipio::find()->where(['iddepartamento' => $id])->all();

        echo "<option required>Seleccione...</option>";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "<option value='$row->idmunicipio' required>$row->municipio</option>";
            }
        }
    }
    //PROCESO QUE CONSULTA
     public function actionExcelconsulta($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', 'TIPO')
                    ->setCellValue('C1', 'DOCUMENTO')
                    ->setCellValue('D1', 'DV')
                    ->setCellValue('E1', 'RAZON SOCIAL')
                    ->setCellValue('F1', 'DIRECCION')
                    ->setCellValue('G1', 'TELEFONO')  
                    ->setCellValue('H1', 'CELULAR')
                    ->setCellValue('I1', 'EMAIL')
                    ->setCellValue('J1', 'DEPARTAMENTO')
                    ->setCellValue('K1', 'MUNICIPIO')
                    ->setCellValue('L1', 'OBSERVACION');
                         
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idproveedor)
                    ->setCellValue('B' . $i, $val->tipo->tipo)
                    ->setCellValue('C' . $i, $val->cedulanit)
                    ->setCellValue('D' . $i, $val->dv)
                    ->setCellValue('E' . $i, $val->nombrecorto)
                    ->setCellValue('F' . $i, $val->direccionproveedor)
                    ->setCellValue('G' . $i, $val->telefonoproveedor)
                    ->setCellValue('H' . $i, $val->celularproveedor)
                    ->setCellValue('I' . $i, $val->emailproveedor)
                    ->setCellValue('J' . $i, $val->departamento->departamento)
                    ->setCellValue('K' . $i, $val->municipio->municipio)
                    ->setCellValue('L' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Proveedor');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Proveedor.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }

}
