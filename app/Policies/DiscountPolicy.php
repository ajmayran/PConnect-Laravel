<?php

namespace App\Policies;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->user_type === 'distributor';
    }

    public function view(User $user, Discount $discount)
    {
        return $user->distributor && $user->distributor->id === $discount->distributor_id;
    }

    public function create(User $user)
    {
        return $user->user_type === 'distributor';
    }

    public function update(User $user, Discount $discount)
    {
        return $user->distributor && $user->distributor->id === $discount->distributor_id;
    }

    public function delete(User $user, Discount $discount)
    {
        return $user->distributor && $user->distributor->id === $discount->distributor_id;
    }
}