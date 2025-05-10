<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations as HasTranslations;
class Ability extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'effect_entries'];

    protected $fillable = [
        'name',
        'is_main_series',
        'effect_entries',
    ];

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class);
    }   
}
 