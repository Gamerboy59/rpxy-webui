<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RustProxy;
use App\Models\RustProxySetting;
use App\Services\ConfigGenerator;

class RustProxySettingController extends Controller
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
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = DB::table('rust_proxy_settings')->get()->groupBy('section');
        $validRustProxies = RustProxy::has('upstreams')->get();
        return view('rustproxy.settings', compact('sections', 'validRustProxies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings.*.key' => 'required|string|exists:rust_proxy_settings,key',
            'settings.*.value' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $type = $request->input("settings.$index.type");
                    $key = $request->input("settings.$index.key");
    
                    if ($type == 'number' && !is_numeric($value)) {
                        $fail('The value must be a number.');
                    } elseif ($type == 'text' && !is_string($value)) {
                        $fail('The value must be a string.');
                    } elseif ($key == 'default_app') {
                        // Check if RustProxy exists
                        $rustProxyExists = RustProxy::where('id', $value)->exists();
                        // Check if RustProxy has at least one Upstream
                        $rustProxyHasUpstreams = RustProxy::has('upstreams')->where('id', $value)->exists();

                        if (!$rustProxyExists) {
                            $fail('The selected proxy does not exist.');
                        } elseif (!$rustProxyHasUpstreams) {
                            $fail('The selected proxy does not have any upstreams.');
                        }
                    }
                },
            ],
            'settings.*.type' => 'required|string|in:text,checkbox,number,select'
        ]);

        // Update settings
        foreach ($request->settings as $setting) {
            RustProxySetting::where('key', $setting['key'])->update([
                'value' => $setting['type'] === 'checkbox' ? ($setting['value'] ?? '0') : $setting['value'],
                'type' => $setting['type']
            ]);
        }

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('rustproxysettings')->with('success', 'Settings updated successfully');
    }
}
