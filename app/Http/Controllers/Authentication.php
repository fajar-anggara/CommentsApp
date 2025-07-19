<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\AuthenticationRepository;
use App\Http\Requests\CommenterRegisterRequest;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class Authentication
{

    protected $auth;
    protected $userTransformer;
    protected $manager;
    public function __construct(AuthenticationRepository $auth)
    {
        $this->auth = $auth;
        $this->userTransformer = new UserTransformer();
        $this->manager = new Manager();
    }

    public function register(CommenterRegisterRequest $request): JsonResponse
    {
        activity()->withProperties([
            'name' => $request->get('name'),
        ])->log('Will Register user');

        $validated = $request->validated();
        $commenter = $this->auth->addNewCommenter($validated);
        $token = $commenter->createToken('register_user')->plainTextToken;

        $setToken = $this->userTransformer->setToken($token);
        $resource = new Item($commenter, $setToken);
        $data = $this->manager->createData($resource)->toArray();

        activity()->performedOn($commenter)
            ->event('Register')
            ->withProperties([
                'name' => $commenter->name,
                'email' => $commenter->email,
                'token' => $token,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil register Commenter',
            'data' => $data,
        ], 201);
    }

    public function login()
    {

    }

    public function logout()
    {

    }

    public function refresh()
    {

    }

    public function forgotPassword()
    {

    }
}
