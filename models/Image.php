<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class Image extends Model
{
    const EDGE_PIXELS = 30;
    /**
     * @var UploadedFile
     */
    public $image;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['image'], 'file'],
        ];
    }
}