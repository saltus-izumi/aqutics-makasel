<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, RecordsUserStamps, SoftDeletes;

    const ROLE_EDITOR = 1;  // 編集者
    const ROLE_VIEWER = 2;  // 閲覧者
    const ROLE_ADMIN = 3;   // 管理者
    const ROLE_ACCOUNTING = 4;  // 経理担当者
    const ROLE_MASTER = 5;  // マスタ
    const ROLE_BPO = 6;     // BPO
    const ROLE_PROCALL = 7; // プロコール

    const ROLES = [
        self::ROLE_EDITOR => '編集者',
        self::ROLE_VIEWER => '閲覧者',
        self::ROLE_ADMIN => '管理者',
        self::ROLE_ACCOUNTING => '経理担当者',
        self::ROLE_MASTER => 'マスタ',
        self::ROLE_BPO => 'BPO',
        self::ROLE_PROCALL => 'プロコール',
    ];

    const DEPARTMENT_GE = 1;
    const DEPARTMENT_LE = 2;
    const DEPARTMENT_TE = 3;
    const DEPARTMENT_AC = 4;

    const DEPARTMENTS = [
        self::DEPARTMENT_GE => '原復',
        self::DEPARTMENT_LE => 'LE',
        self::DEPARTMENT_TE => 'TE',
        self::DEPARTMENT_AC => '経理',
    ];

    public const LE_USER_ID = 307;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthPasswordName()
    {
        return 'user_password';
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $userName = $this->user_name ?? '';
            $firstName = $this->first_name ?? '';

            return trim($userName . ' ' . $firstName);
        });
    }

    public static function getOptions($department = null)
    {
        $query = self::where('is_hidden', false);
        if ($department) {
            $query->whereRaw('FIND_IN_SET(?, departments)', [$department]);
        }
        $users = $query->get();

        $options = [];
        foreach ($users as $user) {
            $options[$user->id] = $user->id . ':' . $user->full_name;
        }

        return $options;
    }

    protected function departmentsArray(): Attribute
    {
        return Attribute::get(function () {
            return explode(',', ($this->departments ?? ''));
        });
    }

    public function isGe() {
        return in_array(self::DEPARTMENT_GE, $this->departments_array);
    }

    public function isLe() {
        return in_array(self::DEPARTMENT_LE, $this->departments_array);
    }

    public function isTe() {
        return in_array(self::DEPARTMENT_TE, $this->departments_array);
    }

    public function isAc() {
        return in_array(self::DEPARTMENT_AC, $this->departments_array);
    }

}
