<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PuntosMapa extends Model
{
    //
	public function getPhotos(){
		return $this->hasMany("App/Photo","punto_mapa_id");
		
	}
	public function getNotes(){
		return $this->hasMany("App/Note","punto_mapa_id");
	}
	public function getNeeds(){
		return $this->hasMany("App/Need","punto_mapa_id");
	}
	public function getHelps(){
		return $this->hasMany("App/Help","punto_mapa_id");
	}
	
	
}
