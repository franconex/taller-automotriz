<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    /**
     * The table associated with the model.
     *
     * Laravel pluralizes "Rol" as "rols", but the migration creates
     * the table using the correct Spanish plural: "roles".
     */
    protected $table = 'roles';
}
