<?php

namespace backend\modules\staff\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\staff\models\Staff;

/**
 * StaffSearch represents the model behind the search form of `backend\modules\staff\models\Staff`.
 */
class ExternalSearch extends Staff
{
	public $staff_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['faculty_id'], 'integer'],
			
			[['staff_no', 'staff_name'], 'string']
			

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
        $query = Staff::find()->where(['staff_active' => 1]);
		$query->andWhere(['<>', 'faculty_id', Yii::$app->params['faculty_id']]);
		$query->joinWith(['user']);

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

		$query->andFilterWhere(['like', 'user.fullname', $this->staff_name]);

        $query->andFilterWhere(['like', 'staff_no', $this->staff_no]);
		
		$query->andFilterWhere(['faculty_id' => $this->faculty_id]);
		
	
		/* $query->andFilterWhere(['or', 
           // ['<>', 'faculty_id', Yii::$app->params['faculty_id']],
            ['faculty_id' => $this->faculty_id]
        ]);
 */
		
		$dataProvider->sort->attributes['staff_name'] = [
        'asc' => ['user.fullname' => SORT_ASC],
        'desc' => ['user.fullname' => SORT_DESC],
        ]; 

        return $dataProvider;
    }
}