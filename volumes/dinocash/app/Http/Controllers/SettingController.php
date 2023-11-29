<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestSettings;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Redirect;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->first();
        return Inertia::render("Settings", [
            "settings" => $settings,
        ]);
    }

    public function update(RequestSettings $request): RedirectResponse
    {
        $request->settings()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('admin.settings');
    }
}