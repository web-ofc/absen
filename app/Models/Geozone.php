<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geozone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude', 'radius', 'is_active'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_geozones');
    }

}
