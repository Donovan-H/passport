<?php

namespace Laravel\Passport;

use Carbon\Carbon;

class RefreshTokenRepository
{
    /**
     * Creates a new Refresh Token.
     *
     * @param  array  $attributes
     * @return \Laravel\Passport\RefreshToken
     */
    public function create($attributes)
    {
        return Passport::refreshToken()->create($attributes);
    }

    /**
     * Get a token by the given ID.
     *
     * @param  string  $id
     * @return \Laravel\Passport\RefreshToken
     */
    public function find($id)
    {
        return Passport::refreshToken()->where('id', $id)->first();
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param  string  $id
     * @param  int  $userId
     * @return \Laravel\Passport\RefreshToken|null
     */
    public function findForUser($id, $userId)
    {
        return Passport::refreshToken()->where('id', $id)->where('user_id', $userId)->first();
    }

    /**
     * Get the token instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId)
    {
        return Passport::refreshToken()->where('user_id', $userId)->get();
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Laravel\Passport\RefreshToken|null
     */
    public function getValidToken($user, $client)
    {
        return $client->tokens()
                    ->whereUserId($user->getKey())
                    ->where('revoked', 0)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();
    }

    /**
     * Store the given token instance.
     *
     * @param  \Laravel\Passport\RefreshToken  $token
     * @return void
     */
    public function save(RefreshToken $token)
    {
        $token->save();
    }

    /**
     * Revoke an refresh token.
     *
     * @param  string  $id
     * @return mixed
     */
    public function revokeRefreshToken($id)
    {
        return Passport::refreshToken()->where('id', $id)->update(['revoked' => true]);
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param  string  $id
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($id)
    {
        if ($token = $this->find($id)) {
            return $token->revoked;
        }

        return true;
    }
}
