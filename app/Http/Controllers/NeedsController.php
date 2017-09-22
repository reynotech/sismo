<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NeedsController extends Controller
{
    //Esta clase gestionará las necesidades que un punto del mapa pueda tener.
	//Un usuario, (responsable), puede subir varias necesidades.
	//Considero colocarla como un recurso aparte porque un punto puede tener varias necesidades a la vez, no solo una.
	//Las Necesidades tendrían dos estados: 0->Sin Resolver, 1->Resuelto.
}
