<?php

namespace App\Http\Controllers;

use App\Models\NFT;
use App\Models\NFTCategorie;
use App\Models\NFTClassification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NFTController extends Controller
{
    public function index (Request $request)
    {
        $nfts = NFT::query();
        $perPage = $request->has('perPage') ? $request->perPage : 50;
        $page = $request->has('page') ? $request->page: 1;

        if ($request->has('name')) {
            $nfts->where('name', 'like', '%'. $request->name .'%');
        }

        return response()->json(
            $nfts->paginate($perPage,
            '*',
            null,
            $page
            )
        );
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'in:ACTIVE,SUSPEND,INACTIVE',
            'description' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        try {
            $NFT = NFT::create($request->all());
            return response()->json($NFT);
        } catch (Exception $e) {
            return response()->json([
                'messge' => 'Erro ao criar NFT',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function update ($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'in:ACTIVE,SUSPEND,INACTIVE',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Erro ao criar termos!!'
            ], 400);
        }

        try {
            $NFT = NFT::findOrFail($id);
            $NFT->update($request->all());
            return response()->json($NFT);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar NFT',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy ($id)
    {
        $nft = NFT::findOrFail($id);
        try {

            $nft->update(['status' => 'INACTIVE']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao INATIVAR NFT',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getCategorie ()
    {
        $categories = NFTCategorie::all();
        return response()->json($categories);
    }

    public function addCategorie(Request $request)
    {
        try {

            $categorie = NFTCategorie::create($request->all());
            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function updateCategorie($id, Request $request)
    {
        try {
            $categorie = NFTCategorie::findOrFail($id);
            $categorie->update($request->all());
            return response()->json($categorie);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao atualizar categoria'], 400);
        }
    }

    public function removeCategorie($id)
    {
        try {
            $categorie = NFTCategorie::findOrFail($id);
            $categorie->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function getClassifications()
    {
        return response()->json(
            NFTClassification::all()
        );
    }

    public function addClassification(Request $request)
    {
        try {

            $classification = NFTClassification::create($request->all());
            return response()->json($classification);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }
    public function updateClassification($id, Request $request)
    {
        try {

            $classification = NFTClassification::findOrFail($id);
            $classification->update($request->all());
            return response()->json($classification);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }

    public function removeClassification($id)
    {
        try {
            $classification = NFTClassification::findOrFail($id);
            $classification->delete();
            return response()->json(['message' => 'Deletado com sucesso !!']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'message' => 'Erro ao gravar categoria'], 400);
        }
    }
}
