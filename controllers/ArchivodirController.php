<?php

namespace app\controllers;

use app\models\Archivodir;
use app\models\Directorio;
use app\models\Documentodir;
use Yii;

use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\base\Model;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use yii\web\UploadedFile;
use app\models\FormSubirArchivo;
use yii\web\Controller;
use yii\filters\VerbFilter;


class ArchivodirController extends \yii\web\Controller
{

    public function actionIndex($codigo,$numero,$view)
    {
        //if (!Yii::$app->user->isGuest) {

        $table = Archivodir::find()->where(['=','numero',$numero])->andWhere(['=','codigo',$codigo])->orderBy('idarchivodir DESC');
        $count = clone $table;
        $pages = new Pagination([
            'pageSize' => 20,
            'totalCount' => $count->count(),
        ]);
        $model = $table
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $to = $count->count();
        return $this->render('index', [
            'model' => $model,
            'codigo' => $codigo,
            'numero' => $numero,
            'pagination' => $pages,
            'view' => $view,
        ]);
    }

    public function actionSubir()
    {
        $model = new FormSubirArchivo();
        $msg = null;        
        $codigo = Html::encode($_GET["codigo"]);
        $numero = Html::encode($_GET["numero"]);
        $view = Html::encode($_GET["view"]);
        $descripcion = '';
        $documentodir = Documentodir::findOne($numero);
        if ($model->load(Yii::$app->request->post()))
            {
            $model->file = UploadedFile::getInstances($model, 'file');
            $descripcion = $_POST['descripcion'];
            if ($model->file && $model->validate()) {
                $carpeta = 'Documentos/'.$model->numero.'/'.$model->codigo.'/';
                if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }
                foreach ($model->file as $file):
                    if(!file_exists($carpeta . $file->baseName . '.' . $file->extension)){
                        $file->saveAs($carpeta . $file->baseName . '.' . $file->extension);
                        $table = new Archivodir();
                        $table->nombre = $file->baseName.'.'.$file->extension;
                        $table->extension = $file->extension;
                        $table->tamaño = $file->size;
                        $table->tipo = $file->type;
                        $table->numero = $numero;
                        $table->descripcion = $descripcion;
                        $table->codigo = $codigo;
                        $table->iddocumentodir = $documentodir->iddocumentodir;
                        $table->iddirectorio = 1;
                        $table->save(false);
                       $this->redirect([$view."/view",'id' => $codigo]);
                    } else {
                        Yii::$app->getSession()->setFlash('warning', 'Ya existe el nombre y la extesion del archivo que desea subir');    
                    }  
                endforeach;
            }
        }
        if (Yii::$app->request->get("numero")) {
            $model->codigo = $codigo;
            $model->numero = $numero;
            $model->view = $view;
        }

        return $this->render("Subir", ["model" => $model, "msg" => $msg,'view' => $view]);
    }
    public function actionDescargar($id,$numero,$codigo)
    {

            $archivo = Archivodir::findOne($id);
            $directorio = Directorio::findOne($archivo->iddirectorio);
            $carpeta = 'Documentos/'.$numero.'/'.$codigo.'/';
            if (!$this->downloadFile($carpeta, $archivo->nombre, ["pdf", "txt", "docx","xlsx","jpg","png"]))
            {
                //Mensaje flash para mostrar el error
                Yii::$app->getSession()->setFlash('error', 'Error en la descarga.');                
            }

        return $this->render('index', [
            'codigo' => $codigo,
            'numero' => $numero,            
        ]);
    }
    
    public function actionEditar()
    {
        $idarchivodir = Html::encode($_POST["idarchivodir"]);
        $numero = Html::encode($_POST["numero"]);
        $codigo = Html::encode($_POST["codigo"]);
        $view = Html::encode($_POST["view"]);
        if(Yii::$app->request->post()){
            if((int) $idarchivodir)
            {
                $table = Archivodir::findOne($idarchivodir);
                
                if ($table) {
                    $table->descripcion = Html::encode($_POST["descripcion"]);                                                                                
                    $table->update();                       
                    $this->redirect(["archivodir/index",'numero' => $numero,'codigo' => $codigo,'view' => $view]); 
                                        
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            }
        }
        //return $this->render("_formeditardetalle", ["model" => $model,]);
    }
    
    public function actionBorrar($id,$numero,$codigo,$view)
    {
            $archivo = Archivodir::findOne($id);
            $directorio = Directorio::findOne($archivo->iddirectorio);            
            if ($archivo)
            {
                $carpeta = $directorio->ruta.$numero.'/'.$codigo.'/';
                $ruta = $carpeta.$archivo->nombre;
                $archivo->delete();
                unlink($ruta);
                $this->redirect(["archivodir/index",'numero' => $numero,'codigo' => $codigo,'view' => $view]);
            }
        
    }

    private function downloadFile($dir, $file, $extensions=[])
    {
        //Si el directorio existe
        if (is_dir($dir))
        {
            //Ruta absoluta del archivo
            $path = $dir.$file;

            //Si el archivo existe
            if (is_file($path))
            {
                //Obtener información del archivo
                $file_info = pathinfo($path);
                //Obtener la extensión del archivo
                $extension = $file_info["extension"];

                if (is_array($extensions))
                {
                    //Si el argumento $extensions es un array
                    //Comprobar las extensiones permitidas
                    foreach($extensions as $e)
                    {
                        //Si la extension es correcta
                        if ($e === $extension)
                        {
                            //Procedemos a descargar el archivo
                            // Definir headers
                            $size = filesize($path);
                            header("Content-Type: application/force-download");
                            header("Content-Disposition: attachment; filename=$file");
                            header("Content-Transfer-Encoding: binary");
                            header("Content-Length: " . $size);
                            // Descargar archivo
                            readfile($path);
                            //Correcto
                            return true;
                        }
                    }
                }
            }
        }
        //Ha ocurrido un error al descargar el archivo
        return false;
    }
}
