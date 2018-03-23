<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string $price
 *
 * @property Revision[] $revisions
 */
class Product extends ActiveRecord
{
	/**
	 * Approves changes
	 */
	public function approve()
	{
		$attributes = [];
		
		foreach ($this->revisions as $rev) {
			if ($rev->new_value && $rev->attr == 'name') {
				$attributes['name'] = $rev->new_value;
				$rev->new_value = null;
				$rev->save();
			} else if ($rev->new_value && $rev->attr == 'price') {
				$attributes['price'] = $rev->new_value;
				$rev->new_value = null;
				$rev->save();
			}
		}
		if ($attributes) {
			$this->updateAttributes($attributes);
		}
	}
	
	/**
	 * @param $post
	 *
	 * @throws \yii\db\Exception
	 */
	public function modify($post)
	{
		$this->load($post);
		$dirty = $this->getDirtyAttributes();
		
		foreach ($dirty as $key => $value)
		{
			(new Query())->createCommand()->update('revision', ['new_value' => $value], 'product_id = :id AND attr = :attr', [':id' => $this->id, ':attr' => $key])->execute();
		}
	}
	
	/**
	 * @param bool $insert
	 * @param array $changedAttributes
	 */
	public function afterSave($insert, $changedAttributes)
	{
		parent::afterSave($insert, $changedAttributes);
		
		if ($insert) {
			$revName = new Revision();
			$revName->product_id = $this->id;
			$revName->attr = 'name';
			$revName->new_value = $this->name;
			$revName->save();
			
			$revPrice = new Revision();
			$revPrice->product_id = $this->id;
			$revPrice->attr = 'price';
			$revPrice->new_value = $this->price;
			$revPrice->save();
			
			$this->updateAttributes([
				'name' => null,
				'price' => null
			]);
		}
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRevisions()
	{
		return $this->hasMany(Revision::class, ['product_id' => 'id']);
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'product';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'string', 'max' => 100],
			[['price'], 'string', 'max' => 10],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'price' => 'Price',
		];
	}
}