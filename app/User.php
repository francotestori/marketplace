<?php

namespace App;

use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Messagable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role_id', 'activation_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function verified()
    {
        $this->activated = true;
        $this->activation_code = null;
        $this->save();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function addspaces()
    {
        return $this->hasMany(Addspace::class,'editor_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    // Role Handling Methods
    public function isManager()
    {
        return $this->role()->first()->name == 'Manager';
    }

    public function isEditor()
    {
        return $this->role()->first()->name == 'Editor';

    }

    public function isAdvertiser()
    {
        return $this->role()->first()->name == 'Advertiser';
    }

    public function getWallet(){
        return $this->wallet()->first();
    }
}