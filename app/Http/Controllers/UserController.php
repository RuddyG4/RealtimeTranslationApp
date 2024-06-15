<?php

namespace App\Http\Controllers;

use App\Enums\ChatType;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userList = User::when($request->has('userQuery'), function ($query) use ($request) {
            $query->where('first_name', 'ilike', '%' . $request->input('userQuery') . '%')
                ->orWhere('last_name', 'ilike', '%' . $request->input('userQuery') . '%');
        })
            ->with('language')
            ->get();
        return response()->json(['userList' => $userList]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function newUsersToChat(Request $request)
    {
        $privateChatsIds = Chat::where('type', ChatType::PRIVATE)->pluck('id'); 
        $userList = User::whereDoesntHave('chats', function ($query) use ($privateChatsIds) { // Users that doesnt have a private chat started with him
            $query->whereIn('id', $privateChatsIds);
        })
            ->when($request->has('userQuery'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('first_name', 'ilike', '%' . $request->input('userQuery') . '%')
                        ->orWhere('last_name', 'ilike', '%' . $request->input('userQuery') . '%');
                });
            })
            ->with('language')
            ->get();
        return response()->json(['userList' => $userList]);
    }
}
