<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'user_id', 'due_date', 'image_path'
    ];

    // Accesor para formatear la fecha de creación
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    // Accesor para formatear la fecha de actualización
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    // Accesor para formatear la fecha de vencimiento
    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->timezone(config('app.timezone'))->format('Y-m-d') : null;
    }

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }

    public function getImagePathAttribute($value)
    {
        return $value ? Storage::url($value) : asset('imgs/default-image-min.webp');
    }

    // Relación uno a muchos inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación muchos a muchos polimorfica
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }
}
