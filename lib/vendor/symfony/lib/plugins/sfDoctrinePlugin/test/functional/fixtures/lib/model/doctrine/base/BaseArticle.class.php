<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseArticle extends myDoctrineRecord
{
  public function setTableDefinition()
  {
    $this->setTableName('article');
    $this->hasColumn('author_id', 'integer', null, array('type' => 'integer'));
    $this->hasColumn('is_on_homepage', 'boolean', null, array('type' => 'boolean'));
    $this->hasColumn('title', 'string', 255, array('type' => 'string', 'length' => '255'));
    $this->hasColumn('body', 'string', 255, array('type' => 'string', 'length' => '255'));
  }

  public function setUp()
  {
    $this->hasOne('Author', array('local' => 'author_id',
                                  'foreign' => 'id'));

    $i18n0 = new Doctrine_Template_I18n(array('fields' => array(0 => 'title', 1 => 'body')));
    $sluggable1 = new Doctrine_Template_Sluggable(array('fields' => array(0 => 'title'), 'uniqueBy' => array(0 => 'lang', 1 => 'title')));
    $i18n0->addChild($sluggable1);
    $timestampable0 = new Doctrine_Template_Timestampable();
    $this->actAs($i18n0);
    $this->actAs($timestampable0);
  }
}