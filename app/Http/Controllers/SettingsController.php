<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $settings = Setting::all();
        return view('settings.edit', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $type = $request->input("settings.$index.type");
    
                    if ($type == 'number' && !is_numeric($value)) {
                        $fail('The value must be a number.');
                    } elseif ($type == 'text' && !is_string($value)) {
                        $fail('The value must be a string.');
                    }
                },
            ],
            'settings.*.type' => 'required|string|in:text,checkbox,number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update settings
        foreach ($request->settings as $setting) {
            Setting::where('key', $setting['key'])->update([
                'value' => $setting['type'] === 'checkbox' ? ($setting['value'] ?? '0') : $setting['value'],
                'type' => $setting['type'],
            ]);
        }

        // Update profile
        $user = Auth::user();
        $user->email = $request->email;
        $user->name = $request->name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('settings.edit')->with('success', 'Setting and profile have been updated.');
    }
}
