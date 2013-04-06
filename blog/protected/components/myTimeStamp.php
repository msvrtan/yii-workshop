<?php
/**
 * Logs creator and modificator id with according timestamps to the database row
 */

class myTimeStamp extends CActiveRecordBehavior
{
    //.. Name of the column in the table where to write the creater user name
    public $createDate = 'dodao_dtm';

    //.. Name of the column in the table where to write the updater user name
    public $updateDate = 'izmjenio_dtm';


    /**
     * Snima ID korisnika i vrijeme stvaranja/izmjene dokumenta
     */
    public function beforeSave($event)
    {
        if($this->Owner->isNewRecord)
        {
            $this->owner->{$this->createDate} = time();
        }
        else
        {
            $this->owner->{$this->updateDate} = time();
        }
        return parent::beforeSave($event);
    }
}
