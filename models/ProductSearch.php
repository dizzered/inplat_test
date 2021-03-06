<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['id'], 'integer'],
			[['name', 'price'], 'safe'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}
	
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = Product::find();
		
		// add conditions that should always apply here
		
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		
		$this->load($params);
		
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		
		$query->joinWith('revisions');
		
		if (Yii::$app->user->identity == null || (Yii::$app->user->identity && Yii::$app->user->identity->role_id == 3)) {
			$query->joinWith('revisions');
			$query->andWhere('name IS NOT NULL AND price IS NOT NULL');
			$query->andWhere("(revision.attr = 'name' AND revision.new_value IS NULL) OR (revision.attr = 'price' AND revision.new_value IS NULL)");
			$query->groupBy('product.id');
		}
		
		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
		]);
		
		$query->andFilterWhere(['like', 'name', $this->name])
			->andFilterWhere(['like', 'price', $this->price]);
		
		return $dataProvider;
	}
}