<?php


namespace app\components;

use Yii;
use app\models\Pixel;
use yii\db\Expression;

class PixelBoard
{
    const ROW_NUMBER = 30;
    const COL_NUMBER = 30;

    const EXPRESSION_RAND_COLOR = "FLOOR(1 + RAND() * 255)";

    protected $pixels = [];

    public function loadPixels() {
        if (!$this->pixels) {
            $pixels = Pixel::find()->all();
            $this->pixels = $pixels;
        }
        // todo - Группировать их по ряду и колонке или так оставить?
        return $pixels;
    }

    /**
     * Clears all pixels and
     */
    public function refillBoard() {
        Yii::$app->db->createCommand('TRUNCATE TABLE ' . Pixel::tableName() . ';')->execute();
        for ($row = 1; $row < self::ROW_NUMBER; $row++) {
            for ($col = 1; $col < self::COL_NUMBER; $col++) {
                $newPixel = new Pixel();
                $newPixel->row = $row;
                $newPixel->col = $col;
                $newPixel->save();
                // todo - фигово без коллекций :(
            }
        }
    }

    public function randomFill()
    {
        Pixel::updateAll([
            'color_red'   => new Expression(self::EXPRESSION_RAND_COLOR),
            'color_blue'  => new Expression(self::EXPRESSION_RAND_COLOR),
            'color_green' => new Expression(self::EXPRESSION_RAND_COLOR),
        ]);
    }

    public function getColors()
    {
        $pixels = $this->loadPixels();
        $colors = [];
        foreach ($pixels as $pixel) {
            /** @var Pixel $pixel */
            $hexColor = $pixel->getHexColor();
            if ($hexColor == Pixel::NULL_HEX) {
                $hexColor = null;
            }
            $colors[$pixel->row][$pixel->col] = $hexColor;
        }
        return $colors;
    }

    /**
     * @param $pixels
     * @return bool
     * @throws \Throwable
     */
    public function savePixels($pixels)
    {
        if ($pixels) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                // Возможно, каждую лампочку хранить отдельно смысла нет, т.к. это лишь плодить запросы
                // Или вручную собирать один update
                foreach ($pixels as $rowNumber => $row) {
                    foreach ($row as $colNumber => $color) {
                        $pixelColors = $color ? Pixel::fromHexColor($color) : Pixel::NULL_COLOR;
                        Pixel::updateAll($pixelColors, ['row' => $rowNumber, 'col' => $colNumber]);
                    }
                }
                $transaction->commit();
                return true;

                // todo - Здесь можно отправить запрос на зажигание лампочек
            } catch (\Throwable $throwable) {
                $transaction->rollBack();
                throw $throwable;
            }
        }
    }

}