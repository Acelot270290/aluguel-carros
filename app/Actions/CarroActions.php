<?php

namespace App\Actions;

use App\Models\Carro;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CreateCarroRequest;
use App\Http\Requests\UpdateCarroRequest;

class CarroActions
{
    public function createCarro(CreateCarroRequest $request)
    {
        try {
            $imgUrl = null;
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = date('YmdHis') . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('carros');

                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                try {
                    $file->move($destinationPath, $filename);
                    $imgUrl = url('carros/' . $filename);
                } catch (\Exception $e) {
                    Log::error('File storage error', ['message' => $e->getMessage()]);
                    return response()->json(['error' => 'File storage error: ' . $e->getMessage()], 500);
                }
            }

            $carro = Carro::create([
                'modelo' => $request->modelo,
                'marca' => $request->marca,
                'img' => $imgUrl,
                'alugado' => $request->alugado ?? false,
            ]);

            return response()->json($carro, 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Erro ao criar carro'], 500);
        }
    }

    public function updateCarro(UpdateCarroRequest $request, Carro $carro)
    {
        try {
            $imgUrl = $carro->img;
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $filename = date('YmdHis') . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('carros');

                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                try {
                    $file->move($destinationPath, $filename);
                    $imgUrl = url('carros/' . $filename);
                } catch (\Exception $e) {
                    Log::error('File storage error', ['message' => $e->getMessage()]);
                    return response()->json(['error' => 'File storage error: ' . $e->getMessage()], 500);
                }
            }

            $carro->update([
                'modelo' => $request->modelo,
                'marca' => $request->marca,
                'img' => $imgUrl,
                'alugado' => $request->alugado ?? $carro->alugado,
            ]);

            return response()->json($carro);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Erro ao atualizar carro'], 500);
        }
    }

    public function deleteCarro($id)
    {
        try {
            $carro = Carro::findOrFail($id);

            $carro->delete();
            return response()->json(['success' => 'Carro deletado com sucesso']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Carro não encontrado'], 404);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Erro ao deletar carro'], 500);
        }
    }

    public function listCarros()
    {
        try {
            $carros = Carro::all();
            return response()->json($carros);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Erro ao listar carros'], 500);
        }
    }

    public function getCarro($id)
    {
        try {
            $carro = Carro::findOrFail($id);
            return response()->json($carro);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Carro não encontrado'], 404);
        }
    }
}
