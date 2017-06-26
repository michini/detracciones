<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Detraccion;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $fillable = ['nombre','nro_cuenta','ruc'];

    public function detracciones(){
    	return $this->hasMany(Detraccion::class);
    }
}
