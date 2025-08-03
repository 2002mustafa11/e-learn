<?php
namespace App\Repositories;
use App\Models\ParentProfile;

class ParentRepository
{
    public function index(){
        return ParentProfile::whereHas('user', function ($query) {
            $query->where('role', 'parent');
        })->with('user')->get();;
    }

    public function show($userId){
        return ParentProfile::where('user_id', $userId)->with('user')->first();
    }

    public function update($userId,$data){
        $parent = ParentProfile::where('user_id', $userId)->with('user')->first();
        $parent->update($data);
        $parent->user->update($data['user']);
        return $parent;
    }

    public function delete($userId){
        $parent = ParentProfile::where('user_id', $userId)->with('user')->first();
        $parent->delete();
        $parent->user()->delete();
        return true;
    }
}