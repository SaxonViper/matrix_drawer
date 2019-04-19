<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pixel".
 *
 * @property int $id
 * @property int $row
 * @property int $col
 * @property int $color_red
 * @property int $color_green
 * @property int $color_blue
 * @property int $opacity
 * @property int $updated_at
 */
class Pixel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pixel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['row', 'col'], 'required'],
            [['row', 'col', 'color_red', 'color_green', 'color_blue', 'opacity', 'updated_at'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'row' => 'Row Number',
            'col' => 'Column Number',
            'color_red' => 'Color Red',
            'color_green' => 'Color Green',
            'color_blue' => 'Color Blue',
            'opacity' => 'Opacity',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return string
     */
    public function getHexColor()
    {
        $red = self::decToHex($this->color_red);
        $green = self::decToHex($this->color_green);
        $blue = self::decToHex($this->color_blue);

        return "#{$red}{$green}{$blue}";
    }

    /**
     * @param int $decimal
     * @return string
     */
    private static function decToHex($decimal) {
        $hex = dechex($decimal);
        if (strlen($hex) == 1) {
            $hex = '0' . $hex;
        }
        return $hex;
    }
}
