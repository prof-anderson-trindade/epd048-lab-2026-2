<?php

namespace App\Policies;

use App\Models\Recado;
use App\Models\User;

class RecadoPolicy
{
    /**
     * @return bool
     */
    public function delete(User $user, Recado $recado)
    {
        return $user->id === $recado->user_id;
    }
}
