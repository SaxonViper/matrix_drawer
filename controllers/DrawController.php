<?php

namespace app\controllers;

use app\components\PixelBoard;
use yii\web\Controller;
use yii\helpers\Json;

class DrawController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->enableCsrfValidation = false;
    }

    public function actionIndex()
    {
        $pixelBoard = new PixelBoard();
        return $this->renderPartial('index', ['pixelColors' => $pixelBoard->getColors()]);
    }

    public function actionRandom()
    {
        $pixelBoard = new PixelBoard();
        $pixelBoard->randomFill();
        $this->redirect('/');
    }

    public function actionPixels()
    {
        $pixelBoard = new PixelBoard();
        return $this->asJson($pixelBoard->getColors());
    }

    public function actionSave()
    {
        $pixels = \Yii::$app->request->post('pixels', '');
        $pixels = Json::decode($pixels);
        $pixelBoard = new PixelBoard();
        $pixelBoard->savePixels($pixels);
        return $this->asJson(['success' => true]);
    }

    public function actionImage()
    {
        // $image = 'https://ru.vuejs.org/images/logo.png';
        // todo - Сделать логику, вернуть и обработать ответ
        $image = \Yii::$app->request->post('image');


    }

}
