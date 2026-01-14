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

    protected function fullName(): Attribute
    {
        return Attribute::get(function () {
            $userName = $this->user_name ?? '';
            $firstName = $this->first_name ?? '';

            return trim($userName . ' ' . $firstName);
        });
    }

    public function getAuthPasswordName()
    {
        return 'user_password';
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

}
