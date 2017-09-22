<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\PuntoMapa;
use App\Note;

class PuntosMapaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
		
        //Logica de validacion:
		$validator=Validator::make($request->all(),
			[
				"direccion"=>"required|max:128",
				"email"=>"nullable|email",
				"afectados"=>"numeric|min:0",
				"telefono"=>"nullable|numeric",
				"url"=>"url",
				"tipo"=>"required|numeric|between:0,1",		//Necesidad o Punto de Acopio -> Colaboracion
				"lat"=>"required|numeric",
				"lng"=>"required|numeric",
				"photo"=>"nullable|mimes:jpg,png,bmp|size:3000"	//Maximo 3 Mb
			];
		if($validator->fails()){
			return $this->error($validator->messages());
		}
		
		$PuntoMapa = new PuntoMapa;
		$PuntoMapa->direccion = $request->direccion;
		$PuntoMapa->email = $request->email;
		$PuntoMapa->afectados = $request->afectados;
		$PuntoMapa->telefono = $request->telefono;
		$PuntoMapa->url = $request->url;
		$PuntoMapa->tipoId=$request->tipo;
		$PuntoMapa->lat = $request->lat;
		$PuntoMapa->lng=$request->lng;
		
		//Upladea la imagen:
		if($request->hasFile("photo")){
			if(!$request->photo->isValid()){
				//Logica para manejar error de subida de fotos:
				$this->error("The uploaded picture couldn't be uploaded ");
			}
			$PuntoMapa->img=$request->photo->store("uploads");
			
		}
		//Checkea si el usuario es un admin, y, si es así, lo coloca en estado confirmado.
		//Sino, lo deja en estado por confirmar
		//(0: por confirmar, 1: confirmado)
		//Roles: 0. usuario, 1. admin
		if($request->session()->has("user.rol") && $request->session()->get("user.rol")==1){
			$PuntoMapa->estadoCodigo=1;
			$this->sendPointMap($PuntoMapa);
		}else{
			$PuntoMapa->estadoCodigo=0;
		}
		
		$PuntoMapa->store();
		return $this->ok();		
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Muestra un mapa o un not found. Tambien busca todas las fotos pertenecientes a un PuntoMapa , sus Notes y sus Needs.
		try{
			
			$PuntoMapa= PuntoMapa::findOrFail($id);
			$Needs=  $PuntoMapa->getNeeds();
			$Photos= $PuntoMapa->getPhotos();
			$Helps=  $PuntoMapa->getHelps();
			$Notes=  $PuntoMapa->getNotes();
			return View::make('puntosmapa.show')
							->with('puntoMapa',$PuntoMapa);
							->with('photos',$Photos);
							->with('helps',$Helps);
							->with('notes',$Notes);
			
		}catch(ModelNotFoundException $e){
			return View::make('puntosmapa.notfound');
		}	
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
		//TODO:  Sistema de roles, que solo un admin lo pueda editar.
		//TODO:  Estudiar opción de que el user que lo creó pueda editar tmb.
		//				=>contras:	Implica que todos los users que reporten deben hacerse cuentas.
		
        try{
			
			$PuntoMapa= PuntoMapa::findOrFail($id);
			return View::make('puntosmapa.edit')->with('puntoMapa',$PuntoMapa);
			
		}catch(ModelNotFoundException $e){
			return View::make('puntosmapa.notfound');
		}
		
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
		//TODO: Sistema de roles. Mismo que arriba.
        try{
			$PuntoMapa= PuntoMapa::findOrFail($id);
			//Logica de validacion:
		$validator=Validator::make($request->all(),
			[
				"direccion"=>"required|max:128",
				"email"=>"nullable|email",
				"afectados"=>"numeric|min:0",
				"telefono"=>"nullable|numeric",
				"url"=>"url",
				"tipo"=>"required|numeric|between:0,1",		//Necesidad o Punto de Acopio -> Colaboracion
				"lat"=>"required|numeric",
				"lng"=>"required|numeric",
				"photo"=>"nullable|mimes:jpg,png,bmp|size:3000"	//Maximo 3 Mb
			];
		if($validator->fails()){
			return $this->error($validator->messages());
		}
		$PuntoMapa->direccion = $request->direccion;
		$PuntoMapa->email = $request->email;
		$PuntoMapa->afectados = $request->afectados;
		$PuntoMapa->telefono = $request->telefono;
		$PuntoMapa->url = $request->url;
		$PuntoMapa->tipoId=$request->tipo;
		$PuntoMapa->lat = $request->lat;
		$PuntoMapa->lng=$request->lng;
		
		//Upladea la imagen:
		if($request->hasFile("photo")){
			if(!$request->photo->isValid()){
				//Logica para manejar error de subida de fotos:
				$this->error("The uploaded picture couldn't be uploaded ");
			}
			$PuntoMapa->img=$request->photo->store("uploads");	
		}
		$PuntoMapa->save();
		$this->ok();
			
		}catch(ModelNotFoundException $e){
			$this->error("Punto de Mapa incorrecto.")
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	/**
	 * Confirma un punto de acopio / punto de daño (Solo admins)
	 *
	 */
	public function confirm($id){
		
		
	}
	
	
	
	//Retorna una respuesta positiva si se guardo correctamente.
	private function ok(){
		return Response::json([
			"ok"=>1
		]);
	}
	
	//Retorna una respuesta negativa
	private function error($error){
		return Response::json([
			"ok"=>0,
			"error"=>$msg
		]);
	}
	
	
	//TODO: enviar punto a cartoDB:
	private function sendPointMap($PuntoMapa){
		
	}
	
}
