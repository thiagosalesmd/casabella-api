<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index (Request $request)
    {
        $perPage = $request->has('perPage') ? $request->perPage: 50;
        $page = $request->has('page') ? $request->page : 1;

        $campaigns = Campaign::with('categories', 'groups', 'user_id');

        if ($request->has('title')) {
            $campaigns->where('title', 'like', '%'. $request->title. '%');
        }

        return response()->json(
            $campaigns->paginate($perPage, '*', null, $page)
        );
    }

    public function store (Request $request)
    {
        $user = auth()->guard('api')->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'categories' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível sincronizar categoria.'
            ], 400);
        }


        try {
            $campaign = Campaign::create(array_merge($data, ["created_by" => $user->id] ));
            $this->syncCategorie($campaign, $data['categories']);

            if (isset($data['groups'])) {
                $this->syncGroup($campaign, $data['groups']);
            }

            if (isset($data['users'])) {
                $this->syncUser($campaign, $data['users']);
            }
            $campaign->categories;
            $campaign->groups;
            $campaign->users;

            return response()->json($campaign);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function update ($campaignId, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'categories' => 'required',
            'groups' => 'required',
            'users' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(),
                'message' => 'Desculpe, não foi possível sincronizar categoria.'
            ], 400);
        }

        $categories = $data['categories'];
        $users = $data['users'];
        $groups = $data['groups'];
        unset($data['users']);
        unset($data['groups']);

        try {
            $campaign = Campaign::findOrFail($campaignId);
            $Categories = $campaign->categories;
            $Groups = $campaign->groups;
            $Users = $campaign->users;

            $campaign->update($data);

            if ($Categories) {
                $this->syncCategorie($campaign, $categories);
            }

            if ($Groups) {
                $this->syncGroup($campaign, $groups);
            }

            if ($Users) {
                $this->syncUser($campaign, $users);
            }

            return response()->json($campaign);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy ($id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
            $Categories = $campaign->categories;
            $Groups = $campaign->groups;
            $Users = $campaign->users;
            if ($Categories) {
                foreach ($Categories as $categorie) {
                    $categorie->delete();
                }
            }
            if ($Groups) {
                foreach ($Groups as $group) {
                    $group->delete();
                }
            }
            if ($Users) {
                foreach ($Users as $user) {
                    $user->delete();
                }
            }
            $campaign->delete();
            return response()->json(['message' => 'Deletado com sucesso']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function syncCategorie(Campaign $campaign, $data)
    {

        try {
            $categories = $campaign->categories()->sync($data);
            return $categories;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    protected function syncGroup(Campaign $campaign, $data)
    {

        try {
            $categories = $campaign->groups()->sync($data);
            return $categories;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    protected function syncUser(Campaign $campaign, $data)
    {

        try {
            $categories = $campaign->users()->sync($data);
            return $categories;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
