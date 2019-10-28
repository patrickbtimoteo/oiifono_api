<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $fillable = ['id', 'person_id', 'city_id', 'address', 'district', 'zipcode', 'complements'];
    public $timestamps = false;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
