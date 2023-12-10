<?php

namespace App\Http\Controllers;

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
        return Inertia::render("Admin/Settings", [
            "settings" => $settings,
        ]);
    }

    public function update(Request $request)
    {
        Setting::first()->update($request->json()->all());
        // $request->settings()->fill($request->validated());

        // $request->user()->save();

        return response()->json(
            [
                "status" => "success",
                "message" => "Settings updated successfully",
            ]
        );
    }

    public function changePayout(Request $request)
    {
        Setting::first()->update(['payout' => $request->payout]);
        return response()->json(
            [
                "status" => "success",
                "message" => "Settings updated successfully",
            ]
        );
    }
}
