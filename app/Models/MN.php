<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
 *          $table->increments('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->bigInteger('author_id');
            $table->bigInteger('section_id')->nullable();
            $table->text('answer')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('answer_date')->nullable();
            $table->timestamps(); //created_at
            $table->boolean('no_active')->nullable();  //будем применять для "удаления" МН
 */
class MN extends Model
{
    /**
   * Связанная с моделью таблица.
   *
   * @var string
   */
    protected $table = 'mn';
  
    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
    
    public function signed_users()
    {
        return $this->belongsToMany('App\User', 'mn_user__rs', 'mn_id', 'user_id');
    }
    
    public function signed_user_IDs()
    {
        return $this->hasMany("App\Models\mn_user_R", "mn_id");
    }
    
    /*public function getArSignedUsersID(){
        $res = [];
        $objRel = $this->signed_user_IDs;
        foreach($objRel as $rel){
            $res[] = $rel->user_id;
        }
        return implode(",",$res);
    }*/

    public function signed_groups()
    {
        return $this->belongsToMany('App\Models\Group');
    }

}
