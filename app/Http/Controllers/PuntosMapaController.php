<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
		
		$request->validate([
			"direccion"=>"required|max:128",
			"email"=>"nullable|email",
			"afectados"=>"numeric|min:0",
			"telefono"=>"nullable|numeric",
			"url"=>"url",
			"tipo"=>"required|numeric|between:0,1",		//Necesidad o Punto de Acopio -> Colaboracion
			"lat"=>"required|numeric",
			"lng"=>"required|numeric",
			"photo"=>"nullable|mimes:jpg,png,bmp|size:3000"	//Maximo 3 Mb
			
		]);
		
		$PuntoMapa = new PuntoMapa;
		$PuntoMapa->direccion = $request->direccion;
		$PuntoMapa->email = $request->email;
		$PuntoMapa->afectados = $request->afectados;
		$PuntoMapa->telefono = $request->telefono;
		$PuntoMapa->url = $request->url;
		$PuntoMapa->tipo=$request->tipo;
		$PuntoMapa->lat = $request->lat;
		$PuntoMapa->lng=$request->lng;
		
		//Upladea la imagen:
		if($request->hasFile("photo")){
			!if($request->photo->isValid()){
				//Logica para manejar error de subida de fotos:
			}
			$PuntoMapa->img=$request->photo->store("uploads");
			
		}
		//Checkea si el usuario es un admin, y, si es así, lo coloca en estado confirmado.
		//Sino, lo deja en estado por confirmar
		//(0: por confirmar, 1: confirmado)
		//Roles: 0. usuario, 1. admin
		if($request->session()->has("user.rol") && $request->session()->get("user.rol")==1){
			$PuntoMapa->status_id=1;
			$this->sendPointMap($PuntoMapa);
		}else{
			$PuntoMapa->status_id=0;
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
        //Muestra un solo punto, con 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
	private function error($msg){
		return Response::json([
			"ok"=>0,
			"msg"=>$msg
		]);
	}
	
	
	//TODO: enviar punto a cartoDB:
	private function sendPointMap($PuntoMapa){
		
	}
	
}
