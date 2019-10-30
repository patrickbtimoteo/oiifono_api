<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Protocol;
use App\Entities\Option;
use App\Helpers\Traits\Models\ModelUuidTrait;
class Question extends Model
{
    use ModelUuidTrait;

    private static $uuidFields = ['label'];
    protected $fillable = ['id', 'label', 'lines', 'description'];
    public $timestamps = false;


    public function protocols()
    {
        return $this->belongsToMany(Protocol::class, 'protocol_questions')->withPivot(['order']);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'question_options')->withPivot(['group']);
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model["id"] = self::generateUuid($model);
        });
    }
}
