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
    public function roles()
    {
        return $this->belongsToMany('App\Role','company_role_user')->withPivot('company_id','assigned_by','assigned_at', 'unassigned_by', 'unassigned_at','remark','status');
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
}
