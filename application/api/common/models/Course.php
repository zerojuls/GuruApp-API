<?php

namespace api\common\models;

use Yii;
use api\models\User;
/**
 * This is the model class for table "course".
 *
 * @property integer $id
 * @property string $name
 * @property integer $amount
 * @property integer $institute_id
 * @property integer $instructor_id
 * @property integer $coursetype_id
 * @property string $strategy
 * @property integer $validity
 *
 * @property Institute $institute
 * @property Users $instructor
 * @property CourseType $coursetype
 * @property CourseDiscussion[] $courseDiscussions
 * @property CourseRating[] $courseRatings
 * @property DailyTest[] $dailyTests
 * @property Notification[] $notifications
 * @property Subject[] $subjects
 * @property UserHasCourse[] $userHasCourses
 */
class Course extends \api\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'institute_id', 'instructor_id', 'coursetype_id'], 'required'],
            [['amount', 'institute_id', 'instructor_id', 'coursetype_id', 'validity'], 'integer'],
            [['strategy'], 'string'],
            [['name'], 'string', 'max' => 255]
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
            'amount' => 'Amount',
            'institute_id' => 'Institute ID',
            'instructor_id' => 'Instructor ID',
            'coursetype_id' => 'Coursetype ID',
            'strategy' => 'Strategy',
            'validity' => 'Validity',
        ];
    }

    public function fields() {
        return [
            'id',
            'name',
            'amount',
            'strength' => function () {
                return intval(Course::getUserHasCourses()->count());
            },
            'rating' => function () {
                $rating = Course::getCourseRatings()->average('[[rating]]'); 
                return isset($rating) ? doubleval($rating) : 0;
            },
            'reviews' => function () {
                return Course::getCourseRatings()->andWhere('review IS NOT NULL')->all();
               
            },
            'institute' => function () {
                return Course::getInstitute()->addSelect(["id","name"])->one();
            },
            'instructor' => function () {
                return Course::getInstructor()->addSelect(["id","first_name","last_name"])->one();
            },
            'coursetype' => function () {
                return Course::getCoursetype()->one();
            },
            'strategy',
            'validity',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitute()
    {
        return $this->hasOne(Institute::className(), ['id' => 'institute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructor()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'instructor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoursetype()
    {
        return $this->hasOne(CourseType::className(), ['id' => 'coursetype_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseDiscussions()
    {
        return $this->hasMany(CourseDiscussion::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseRatings()
    {
        return $this->hasMany(CourseRating::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDailyTests()
    {
        return $this->hasMany(DailyTest::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserHasCourses()
    {
        return $this->hasMany(UserHasCourse::className(), ['courseId' => 'id']);
    }
}

class CourseQuery extends \api\components\db\ActiveQuery
{
}