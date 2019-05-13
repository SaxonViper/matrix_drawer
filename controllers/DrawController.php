<?php

namespace app\controllers;

use app\components\PixelBoard;
use app\models\Image;
use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use yii\web\UploadedFile;

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

        $model = new Image();
        $result = [];

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->image = UploadedFile::getInstance($model, 'image');

            if ($model->image && $model->validate()) {
                $fileLocalPath = Yii::getAlias('@app/runtime') . '/' . $model->image->name;
                $model->image->saveAs($fileLocalPath);

                /** @var \Imagick $img */
                $img = new \Imagick($fileLocalPath);

                $height = $img->getImageHeight();
                $width = $img->getImageWidth();

                $fullImg = new \Imagick();
                $fullImg->newImage(Image::EDGE_PIXELS, Image::EDGE_PIXELS, new \ImagickPixel('black'));

                if ($height > Image::EDGE_PIXELS || $width > Image::EDGE_PIXELS) {
                    if ($height > $width) {
                        $width = Image::EDGE_PIXELS * $width / $height;
                        $width = $width > Image::EDGE_PIXELS ? Image::EDGE_PIXELS : round($width);
                        $height = Image::EDGE_PIXELS;
                    } else {
                        $height = Image::EDGE_PIXELS * $height / $width;
                        $height = $height > Image::EDGE_PIXELS ? Image::EDGE_PIXELS : round($height);
                        $width = Image::EDGE_PIXELS;
                    }
                    $img->resizeImage($width, $height, \Imagick::FILTER_UNDEFINED, 1.2);
                }

                $fullImg->compositeImage($img, \Imagick::ALIGN_CENTER, round((Image::EDGE_PIXELS - $width) / 2), round((Image::EDGE_PIXELS - $height) / 2));

                $iterator = $fullImg->getPixelIterator();
                foreach ($iterator as $row=>$pixels) {
                    foreach ( $pixels as $col=>$pixel) {
                        $color = $pixel->getColor();      // values are 0-255
                        $alpha = $pixel->getColor(true);  // values are 0.0-1.0

                        $r = $color['r'];
                        $g = $color['g'];
                        $b = $color['b'];
                        $a = $alpha['a'];

                        $result[$row + 1][$col + 1] = '#'.dechex($r).dechex($g).dechex($b);
                    }
                }

                $img->clear();
                $fullImg->clear();
                if (file_exists($fileLocalPath)) {
                    unlink($fileLocalPath);
                }
            }
        }
        return $this->asJson(['pixels' => $result, 'h' => $height, 'w' => $width]);
    }

}
