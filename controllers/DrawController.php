<?php

namespace app\controllers;

use app\components\PixelBoard;
use yii\web\Controller;

class DrawController extends Controller
{
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

}
