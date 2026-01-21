<?php

namespace App\Models;

use App\Models\Concerns\RecordsUserStamps;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Authenticatable
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
        'mail',
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
            'password' => 'hashed',
        ];
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    /**
     * このオーナーが持つ家主（Landlord）を取得
     */
    public function landlords()
    {
        return $this->hasMany(Landlord::class);
    }

    public static function getOptions()
    {
        $owners = self::get();

        $options = [];
        foreach ($owners as $owner) {
            $options[$owner->id] = $owner->id . ':' . $owner->name;
        }

        return $options;
    }
}
