<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('targetforce::profile.show');
    }

    public function edit(): View
    {
        return view('targetforce::profile.edit');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->back()->with('success', __('Your profile was updated successfully!'));
    }
}
