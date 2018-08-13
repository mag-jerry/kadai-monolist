<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function items() {
        return $this->belongsToMany(Item::class)->withpivot('type')->withTimestamps();
    }
    
    public function want_items() {
        return $this->items()->where('type','want');
    }
    
    public function have_items() {
        return $this->items()->where('type','have');
    }
    
    public function want($itemID){
        
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemID);
        
        if($exist){
            // 既に Want していれば何もしない
            return false;
        }else{
            // 未 Want であれば Want する
            $this->items()->attach($itemID,['type' => 'want']);
            return true;
        }
    }
    
    public function dont_want($itemID){
        
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemID);
        
        if($exist){
            // 既に Want していれば Want を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'",[$this->id,$itemID]);
        }else{
            // 未 Want であれば何もしない
            return false;
        }
    }
    
    
    //Have追加
    public function have($itemID){
        
        // 既に Have しているかの確認
        $exist = $this->is_having($itemID);
        
        if($exist){
            // 既に have していれば何もしない
            return false;
        }else{
            // 未 Have であれば Have する
            $this->items()->attach($itemID,['type' => 'have']);
            return true;
        }
    }
    
    public function dont_have($itemID){
        
        // 既に Have しているかの確認
        $exist = $this->is_having($itemID);
        
        if($exist){
            // 既に Have していれば Have を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'",[$this->id,$itemID]);
        }else{
            // 未 Have であれば何もしない
            return false;
        }
    }
    
    
    public function is_wanting($itemIdOrCode){
        if(is_numeric($itemIdOrCode)){
            $item_id_exists = $this->want_items()->where('item_id',$itemIdOrCode)->exists();
            return $item_id_exists;
        }else{
            $item_code_exists = $this->want_items()->where('code',$itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    //Have追加
    public function is_having($itemIdOrCode){
        if(is_numeric($itemIdOrCode)){
            $item_id_exists = $this->have_items()->where('item_id',$itemIdOrCode)->exists();
            return $item_id_exists;
        }else{
            $item_code_exists = $this->have_items()->where('code',$itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
}
