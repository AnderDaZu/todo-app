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
        return Carbon::parse($value)->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    protected function title(): Attribute
    {
        return new Attribute(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value),
        );
    }

    protected function image(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->image_path) {
                    return Storage::url($this->image_path);
                } else {
                    // cuando no se adjunta imagen a una nota, se muestra una por defecto
                    return asset('imgs/default-image-min.webp');
                }
            },
        );
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
