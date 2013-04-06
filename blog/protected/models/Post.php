<?php

/**
 * This is the model class for table "{{post}}".
 *
 * The followings are the available columns in table '{{post}}':
 * @property integer   $id
 * @property string    $title
 * @property string    $content
 * @property string    $tags
 * @property integer   $status
 * @property integer   $create_time
 * @property integer   $update_time
 * @property integer   $author_id
 *
 * The followings are the available model relations:
 * @property Comment[] $comments
 * @property User      $author
 */
class Post extends CActiveRecord
{
    const STATUS_DRAFT=1;
    const STATUS_PUBLISHED=2;
    const STATUS_ARCHIVED=3;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return Post the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{post}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, content, status', 'required'),
            array('title', 'length', 'max' => 128),
            array('status', 'in', 'range' => array(1, 2, 3)),
            array(
                'tags',
                'match',
                'pattern' => '/^[\w\s,]+$/',
                'message' => 'Tags can only contain word characters.'
            ),
            array('tags', 'normalizeTags'),
            array('title, status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.

        return array(
            'author'       => array(self::BELONGS_TO, 'User', 'author_id'),
            'comments'     => array(
                self::HAS_MANY,
                'Comment',
                'post_id',
                'condition' => 'comments.status=' . Comment::STATUS_APPROVED,
                'order'     => 'comments.create_time DESC'
            ),
            'commentCount' => array(
                self::STAT,
                'Comment',
                'post_id',
                'condition' => 'status=' . Comment::STATUS_APPROVED
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id'          => 'ID',
            'title'       => 'Title',
            'content'     => 'Content',
            'tags'        => 'Tags',
            'status'      => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'author_id'   => 'Author',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('tags', $this->tags, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('update_time', $this->update_time);
        $criteria->compare('author_id', $this->author_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function normalizeTags($attribute, $params)
    {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->tags)));
    }

    public function behaviors(){

        return array(
            'myTimeStamp' => array(
                'class' => 'application.components.myTimeStamp',
                'createDate' => 'create_time',
                'updateDate' => 'update_time'
            )
        );
    }

    public function actionView()
    {
        $post=$this->loadModel();
        $this->render('view',array(
                'model'=>$post,
            ));
    }

    private $_model;

    public function loadModel()
    {
        if($this->_model===null)
        {
            if(isset($_GET['id']))
            {
                if(Yii::app()->user->isGuest)
                    $condition='status='.Post::STATUS_PUBLISHED
                        .' OR status='.Post::STATUS_ARCHIVED;
                else
                    $condition='';
                $this->_model=Post::model()->findByPk($_GET['id'], $condition);
            }
            if($this->_model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
    }



}