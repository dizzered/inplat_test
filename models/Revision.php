<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "revision".
 *
 * @property int $id
 * @property int $product_id
 * @property string $attr
 * @property string $new_value
 */
class Revision extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'revision';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['product_id', 'attr'], 'required'],
			[['product_id'], 'integer'],
			[['attr'], 'string', 'max' => 50],
			[['new_value'], 'string', 'max' => 100],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'product_id' => 'Product ID',
			'attr' => 'Attr',
			'new_value' => 'New Value',
		];
	}
}