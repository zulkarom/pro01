<?php

namespace common\models;

use Yii;
use backend\modules\erpd\models\Research;
use backend\modules\erpd\models\Publication;
use backend\modules\erpd\models\Membership;
use backend\modules\erpd\models\Award;
use backend\modules\erpd\models\Consultation;
use backend\modules\erpd\models\KnowledgeTransfer;

/**
 * Class Menu
 * Theme menu widget.
 */
class Dashboard
{
	public static function countMyResearch(){
		$kira = Research::find()
		->joinWith('researchers')
		->where(['staff_id' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
	
	public static function countMyCompletedResearch(){
		$kira = Research::find()
		->joinWith('researchers')
		->where(['staff_id' => Yii::$app->user->identity->staff->id, 'status' => 50, 'res_progress' => 1])
		->count();
		return $kira;
	}
	
	public static function countMyOnGoingResearch(){
		$kira = Research::find()
		->joinWith('researchers')
		->where(['staff_id' => Yii::$app->user->identity->staff->id, 'status' => 50, 'res_progress' => 0])
		->count();
		return $kira;
	}
	
	public static function countMyPublication(){
		$kira = Publication::find()
		->joinWith('pubTags')
		->where(['rp_pub_tag.staff_id' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
	
	public static function countMyMembership(){
		$kira = Membership::find()
		->where(['msp_staff' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
	
	public static function countMyAward(){
		$kira = Award::find()
		->where(['awd_staff' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
	
	public static function countMyConsultation(){
		$kira = Consultation::find()
		->where(['csl_staff' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
	
	public static function countMyKtp(){
		$kira = KnowledgeTransfer::find()
		->joinWith('members')
		->where(['rp_knowledge_transfer_member.staff_id' => Yii::$app->user->identity->staff->id, 'status' => 50])
		->count();
		return $kira;
	}
}