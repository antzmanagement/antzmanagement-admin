<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// HasApiTokens will provide a few helper methods to your model
// which allows you to inspect the authenticated user's token and scopes
use Laravel\Passport\HasApiTokens;

/** @OA\Schema(
 *     title="User"
 * )
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    //Customize login condition. Ex : use username to login
     /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->where('status', true)->first();
    }

    /** @OA\Property(property="id", type="integer"),
     * @OA\Property(property="uid", type="string"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="icno", type="string"),
     * @OA\Property(property="tel1", type="string"),
     * @OA\Property(property="tel2", type="string"),
     * @OA\Property(property="address1", type="string"),
     * @OA\Property(property="address2", type="string"),
     * @OA\Property(property="postcode", type="string"),
     * @OA\Property(property="city", type="string"),
     * @OA\Property(property="state", type="string"),
     * @OA\Property(property="country", type="string"),
     * @OA\Property(property="password", type="string"),
     * @OA\Property(property="status", type="string"),
     * @OA\Property(property="last_login", type="string"),
     * @OA\Property(property="last_active", type="string"),
     * @OA\Property(property="remember_token", type="string"),
     * @OA\Property(property="created_at", type="string"),
     * @OA\Property(property="updated_at", type="string")
     */




    protected $fillable = [
        'uid', 'email', 'name', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'last_login' => 'datetime',
        'last_active' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * user roles
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    
    public function usertypes()
    {
        return $this->belongsToMany('App\UserType','users_types', 'user_id', 'user_type_id')->withPivot('remark','status','created_at','updated_at');
    }


    public function ownrooms()
    {
        return $this->belongsToMany('App\Room','owners_rooms', 'user_id', 'room_id')->withPivot('remark','status','created_at','updated_at');
    }


    public function roomcontracts()
    {
        return $this->hasMany('App\RoomContract', 'tenant_id');
    }

    public function manageroomcontracts()
    {
        return $this->hasMany('App\RoomContract', 'pic');
    }

    public function maintenances()
    {
        return $this->hasMany('App\Maintenance', 'tenant_id');
    }

    public function cleanings()
    {
        return $this->hasMany('App\Cleaning', 'tenant_id');
    }
    
    public function ownermaintenances()
    {
        return $this->hasMany('App\Maintenance', 'owner_id');
    }

    public function ownercleanings()
    {
        return $this->hasMany('App\Cleaning', 'owner_id');
    }


    public function claims()
    {
        return $this->hasMany('App\Claim', 'owner_id');
    }
    
    public function creator()
    {
        return $this->belongsTo('App\User', 'pic');
    }
    public function createdUsers()
    {
        return $this->hasMany('App\User', 'pic');
    }

    public function issuerentalpayments()
    {
        return $this->hasMany('App\RentalPayment', 'issueby');
    }

    public function issuepayments()
    {
        return $this->hasMany('App\Payment', 'issueby');
    }

    public function deletedrentalpayments()
    {
        return $this->hasMany('App\RentalPayment', 'deletedby');
    }

    public function deletedpayments()
    {
        return $this->hasMany('App\Payment', 'deletedby');
    }
    
}
