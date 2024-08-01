<?php

namespace App\Http\Controllers;

use App\Actions\CarroActions;
use App\Http\Requests\CreateCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Models\Carro;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    protected $carroActions;

    public function __construct(CarroActions $carroActions)
    {
        $this->carroActions = $carroActions;
    }

    public function create(CreateCarroRequest $request)
    {
        return $this->carroActions->createCarro($request);
    }

    public function update(UpdateCarroRequest $request, Carro $carro)
    {
        return $this->carroActions->updateCarro($request, $carro);
    }

    public function delete($id)
    {
        return $this->carroActions->deleteCarro($id);
    }

    public function list()
    {
        return $this->carroActions->listCarros();
    }

    public function show($id)
    {
        return $this->carroActions->getCarro($id);
    }
}
