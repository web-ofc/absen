<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'default_work_start', 'default_work_end', 
        'require_geozone', 'is_active'
    ];

    protected $casts = [
        'require_geozone' => 'boolean',
        'is_active' => 'boolean',
        'default_work_start' => 'datetime:H:i',
        'default_work_end' => 'datetime:H:i',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function geozones()
    {
        return $this->belongsToMany(Geozone::class, 'division_geozones');
    }
}
