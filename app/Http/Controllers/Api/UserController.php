<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at')->paginate(10);

        return $this->respondOkWithData($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password', 'role']));

        $user->assignRole($request->role);

        return $this->respondOkWithData($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->respondOkWithData($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->safe()->only(['name', 'email', 'password', 'role']));

        if ($request->role) {
            $user->assignRole($request->role);
        }

        $user->refresh();

        return $this->respondOkWithData($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->respondOkWithData($user);
    }
}
