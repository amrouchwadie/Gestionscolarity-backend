<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Niveaus extends Model
{
    protected $fillable = [
        'filiere_id', 'name_niveau',
    ];
}
