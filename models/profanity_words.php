<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profanity_words".
 *
 * @property int $id
 * @property string $Word
 */
class profanity_words extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profanity_words';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Word'], 'required'],
            [['Word'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Word' => 'Word',
        ];
    }
}
