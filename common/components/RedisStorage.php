<?php
/**
 * Created by PhpStorm.
 * User: webwlsong
 * Date: 9/17/15
 * Time: 1:58 PM
 */

namespace common\components;

use common\models\HoorayCount;
use common\models\HoorayLog;
use common\models\HoorayUser;
use common\models\HoorayUserinfo;
use common\models\StuCount;
use common\models\StudentInfo;
use common\models\StuLog;
use common\models\TchCount;
use common\models\TchLog;
use common\models\TeacherInfo;
use common\models\User;
use yii;

class RedisStorage extends yii\redis\ActiveRecord
{
    public static function userinfo($user_id,$group=2)
    {
        if(isset($user_id)){
            $redis_user_info = HoorayUserinfo::findOne(['user_id'=>$user_id]);
            if($redis_user_info){
                return $redis_user_info;
            }else{
                $red_users_info = new HoorayUserinfo();
                if($group == 2){
                    $tch_user_info = TeacherInfo::findOne(['user_id'=>$user_id]);
                    if($tch_user_info){
                        $red_users_info->user_id = $tch_user_info->user_id;
                        $red_users_info->realname = $tch_user_info->realname;
                        $red_users_info->nickname = $tch_user_info->nickname;
                        $red_users_info->gender = $tch_user_info->gender;
                        $red_users_info->birthmonth = $tch_user_info->birthmonth;
                        $red_users_info->birthyear = $tch_user_info->birthyear;
                        $red_users_info->birthday = $tch_user_info->birthday;
                        $red_users_info->resideprovince = $tch_user_info->resideprovince;
                        $red_users_info->residecity = $tch_user_info->residecity;
                        $red_users_info->residecommunity = $tch_user_info->residecommunity;
                        $red_users_info->residesuite = $tch_user_info->residesuite;
                        $red_users_info->telephone = $tch_user_info->telephone;
                        $red_users_info->education = $tch_user_info->education;
                        $red_users_info->profile = $tch_user_info->profile;
                        $red_users_info->avatar = $tch_user_info->avatar;
                        $red_users_info->characteristics = $tch_user_info->characteristics;
                        $red_users_info->SchoolName = $tch_user_info->SchoolName;
                        $red_users_info->GradeName = $tch_user_info->GradeName;
                        $red_users_info->idcard = $tch_user_info->idcard;
                        $red_users_info->save();
                        return $tch_user_info;
                    }else{
                        return false;
                    }
                }elseif($group ==1){
                    $stu_user_info = StudentInfo::findOne(['user_id'=>$user_id]);
                    if($stu_user_info){
                        $red_users_info->user_id = $stu_user_info->user_id;
                        $red_users_info->realname = $stu_user_info->realname;
                        $red_users_info->nickname = $stu_user_info->nickname;
                        $red_users_info->gender = $stu_user_info->gender;
                        $red_users_info->birthmonth = $stu_user_info->birthmonth;
                        $red_users_info->birthyear = $stu_user_info->birthyear;
                        $red_users_info->birthday = $stu_user_info->birthday;
                        $red_users_info->resideprovince = $stu_user_info->resideprovince;
                        $red_users_info->residecity = $stu_user_info->residecity;
                        $red_users_info->residecommunity = $stu_user_info->residecommunity;
                        $red_users_info->residesuite = $stu_user_info->residesuite;
                        $red_users_info->telephone = $stu_user_info->telephone;
                        $red_users_info->education = $stu_user_info->education;
                        $red_users_info->profile = $stu_user_info->profile;
                        $red_users_info->avatar = $stu_user_info->avatar;
                        $red_users_info->GradeName = $stu_user_info->GradeName;
                        $red_users_info->idcard = $stu_user_info->idcard;
                        $red_users_info->save();
                        return $stu_user_info;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    public static function user($user_id)
    {
        if(isset($user_id)){
            $hoorayUser = HoorayUser::findOne(['user_id'=>$user_id]);
            if($hoorayUser){
                unset($hoorayUser->upassword);
                return $hoorayUser;
            }else{
                $users = User::findOne(['user_id'=>$user_id]);
                if($users && isset($users->type)){
                    $redis_users = new HoorayUser();
                    $redis_users->user_id = $users->user_id;
                    $redis_users->user_number = $users->user_number;
                    $redis_users->telephone = $users->telephone;
                    $redis_users->username = $users->username;
                    $redis_users->upassword = $users->upassword;
                    $redis_users->pwsafety = $users->pwsafety;
                    $redis_users->regdate = $users->regdate;
                    $redis_users->avatarstatus = $users->avatarstatus;
                    $redis_users->group_id = $users->group_id;
                    $redis_users->attr_id = $users->attr_id;
                    $redis_users->xstatus = $users->xstatus;
                    $redis_users->FormUserId = $users->FormUserId;
                    $redis_users->GradeName = $users->GradeName;
                    $redis_users->user_source = isset($users->user_source)?$users->user_source:"";
                    $redis_users->xtype = $users->xtype;
                    $redis_users->status = '0';
                    $redis_users->type = isset($users->type)?$users->type:'0';
                    $redis_users->save();
                    unset($users->upassword);
                    return $users;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    public static function userlog($user_id,$group_id)
    {
        if(isset($user_id)){
            $redis_log = HoorayLog::findOne(['user_id'=>$user_id]);
            if($redis_log){
                return $redis_log;
            }else{
                $red_log = new HoorayLog();
                if($group_id ==2) {
                    $log = TchLog::findOne(['user_id' => $user_id]);
                    if ($log) {
                        $red_log->user_id = $log->user_id;
                        $red_log->regip = $log->regip;
                        $red_log->logouttime = $log->logouttime;
                        $red_log->logintime = $log->logintime;
                        $red_log->version = $log->version;
                        $red_log->save();
                        return $log;
                    }else{
                        return false;
                    }
                }elseif($group_id==1) {
                    $log = StuLog::findOne(['user_id' => $user_id]);
                    if ($log) {
                        $red_log->user_id = $log->user_id;
                        $red_log->regip = $log->regip;
                        $red_log->logouttime = $log->logouttime;
                        $red_log->logintime = $log->logintime;
                        $red_log->version = $log->version;
                        $red_log->save();
                        return $log;
                     }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }
    }


    public static function usercount($user_id,$group_id)
    {
        if(isset($user_id)){
            $redis_count = HoorayCount::findOne(['user_id'=>$user_id]);
            if($redis_count){
                return $redis_count;
            }else{
                $red_count = new HoorayCount();
                if($group_id == 2){
                    $count = TchCount::findOne(['user_id'=>$user_id]);
                    if($count) {
                        $red_count->user_id = $count->user_id;
                        $red_count->credits = $count->credits;
                        $red_count->coin = $count->coin;
                        $red_count->lock_coin = $count->lock_coin;
                        $red_count->question_num = $count->question_num;
                        $red_count->coureses_num = $count->coureses_num;
                        $red_count->online_coureses_num = $count->online_coureses_num;
                        $red_count->follower = $count->follower;
                        $red_count->following = $count->following;
                        $red_count->rating = $count->rating;
                        $red_count->favorites = $count->favorites;
                        $red_count->positive = $count->positive;
                        $red_count->moderate = $count->moderate;
                        $red_count->negative = $count->negative;
                        $red_count->CoursePositive = $count->CoursePositive;
                        $red_count->CourseModerate = $count->CourseModerate;
                        $red_count->CourseNegative = $count->CourseNegative;
                        $red_count->AskPositive = $count->AskPositive;
                        $red_count->AskModerate = $count->AskModerate;
                        $red_count->AskNegative = $count->AskNegative;
                        $red_count->comment_num = $count->comment_num;
                        $red_count->comment_sum_rating = $count->comment_sum_rating;
                        $red_count->save();
                        return $count;
                    }else{
                        return false;
                    }
                }elseif($group_id ==1){
                    $count = StuCount::findOne(['user_id'=>$user_id]);
                    if($count) {
                        $red_count->user_id = $count->user_id;
                        $red_count->credits = $count->credits;
                        $red_count->coin = $count->coin;
                        $red_count->lock_coin = $count->lock_coin;
                        $red_count->question_num = $count->question_num;
                        $red_count->coureses_num = $count->coureses_num;
                        $red_count->online_coureses_num = $count->online_coureses_num;
                        $red_count->follower = $count->follower;
                        $red_count->following = $count->following;
                        $red_count->rating = $count->rating;
                        $red_count->favorites = $count->favorites;
                        $red_count->save();
                        return $redis_count;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }
    }
}