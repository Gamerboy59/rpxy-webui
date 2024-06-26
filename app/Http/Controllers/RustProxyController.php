<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RustProxy;
use App\Models\RustProxySetting;
use App\Services\ConfigGenerator;

class RustProxyController extends Controller
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
        $defaultApp = RustProxySetting::where('key', 'default_application')->value('value');
        $proxies = RustProxy::with(['upstreams'])->get();

        return view('rustproxy.list', compact('proxies', 'defaultApp'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'server_name' => ['required', 'string', 'max:255', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/'],
            'ssl_enabled' => 'nullable|boolean',
            'https_redirection' => 'nullable|boolean',
            'tls_cert_path' => 'nullable|string|max:255',
            'tls_cert_key_path' => 'nullable|string|max:255',
            'client_ca_cert_path' => 'nullable|string|max:255',
        ]);

        RustProxy::create($request->all());

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('proxy.index')->with('success', 'Proxy created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RustProxy $rustProxy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RustProxy $rustProxy)
    {
        return view('rustproxy.edit', compact('rustProxy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RustProxy $rustProxy)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'server_name' => ['required', 'string', 'max:255', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/'],
            'ssl_enabled' => 'nullable|boolean',
            'https_redirection' => 'nullable|boolean',
            'tls_cert_path' => 'nullable|string|max:255',
            'tls_cert_key_path' => 'nullable|string|max:255',
            'client_ca_cert_path' => 'nullable|string|max:255',
        ]);

        $rustProxy->update($request->all());

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('proxy.index')->with('success', 'Proxy updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RustProxy $rustProxy)
    {
        $defaultApp = RustProxySetting::where('key', 'default_application')->value('value');
        if ($defaultApp == $rustProxy->id) {
            RustProxySetting::where('key', 'default_application')->update(['value' => '0']);
            $additional_success_msg = ' Resetting default proxy setting.';
        }

        $rustProxy->upstreams()->delete();
        $rustProxy->delete();

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('proxy.index')->with('success', 'Proxy deleted successfully.' . ($additional_success_msg ?? ''));
    }

}
