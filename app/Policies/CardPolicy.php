<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Patient;
class CardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    // سماح للمريض ان يشوف كارت بتاعه
    public function view(User $user, Card $card): bool
    {
  return $user->patient && $user->patient->id === $card->patient_id;
    }

    /**
     * Determine whether the user can create models.
     */

    //اي مريض مسجل يعمل كارت خاص به 
    public function create(User $user): bool
    {
        return $user->patient !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Card $card): bool
    {
        
    return $user->patient && $user->patient->id === $card->patient_id;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Card $card): bool
    {
        // return false;
    return $user->patient && $user->patient->id === $card->patient_id;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Card $card): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Card $card): bool
    {
        return false;
    }
}
