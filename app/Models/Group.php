<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
 *          $table->increments('id');
            $table->timestamps();
            $table->string('name', 100);
            $table->integer('author_id')->unsigned();
 */
class Group extends Model
{
    /**
   * Связанная с моделью таблица.
   *
   * @var string
   */
    protected $table = 'groups';
  
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
    
    public function signed_users()
    {
        return $this->belongsToMany('App\User', 'user_group', 'group_id', 'user_id');
    }

    public function signed_mn()
    {
        return $this->belongsToMany('App\Models\MN', 'mn_group', 'group_id', 'mn_id');
    }

}
