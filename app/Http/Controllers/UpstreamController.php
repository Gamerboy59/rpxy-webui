<?php

namespace App\Http\Controllers;

use App\Models\RustProxy;
use App\Models\RustProxySetting;
use App\Models\Upstream;
use App\Models\LoadbalanceType;
use App\Models\UpstreamLocation;
use App\Models\UpstreamOption;
use App\Services\ConfigGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpstreamController extends Controller
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
    public function index(RustProxy $rustProxy)
    {
        $upstreams = $rustProxy->upstreams()->get();
        $loadbalanceTypes = LoadbalanceType::all();
        return view('upstream.list', compact('rustProxy', 'upstreams', 'loadbalanceTypes'));
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
    public function store(Request $request, RustProxy $rustProxy)
    {
        $request->validate([
            'tls' => 'required|boolean',
            'loadbalance_type_id' => 'required|exists:loadbalance_types,id',
            'path' => 'nullable|string|required_with:replace_path',
            'replace_path' => 'nullable|string|required_with:path',
            'locations' => 'required|array|min:1',
            'locations.*.location' => ['required', 'string', 'max:255', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/'],
            'locations.*.tls' => 'required|boolean'
        ]);

        $upstream = $rustProxy->upstreams()->create($request->only(['tls', 'loadbalance_type_id', 'path', 'replace_path']));

        foreach ($request->locations as $location) {
            $upstream->locations()->create(['location' => $location, 'tls' => $request->tls]);
        }

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('upstream.list', $rustProxy)->with('success', 'Upstream added successfully.');
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
    public function edit(Upstream $upstream)
    {
        $upstream->load(['rustproxy', 'loadbalanceType', 'upstreamOptions', 'locations']);
        $loadbalanceTypes = LoadbalanceType::all();
        $upstreamOptions = UpstreamOption::all();
        return view('upstream.edit', compact('upstream', 'loadbalanceTypes', 'upstreamOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Upstream $upstream)
    {
        $request->validate([
            'tls' => 'required|boolean',
            'loadbalance_type_id' => 'required|exists:loadbalance_types,id',
            'path' => 'nullable|string|required_with:replace_path',
            'replace_path' => 'nullable|string|required_with:path',
            'options' => 'array',
            'options.*' => 'exists:upstream_options,id',
            'locations' => 'required|array|min:1',
            'locations.*.location' => ['required', 'string', 'max:255', 'regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/'],
            'locations.*.tls' => 'required|boolean',
        ]);

        // Custom validation for mutually exclusive options
        if (is_array($request->options)){
            if (in_array('force_http2_upstream', $request->options) && in_array('force_http11_upstream', $request->options)) {
                return redirect()->back()->withErrors(['options' => 'force_http2_upstream and force_http11_upstream cannot be selected together.']);
            }
        }

        $upstream->update($request->only(['tls', 'loadbalance_type_id', 'path', 'replace_path']));

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        $upstream->upstreamOptions()->sync($request->options);

        // Sync locations
        $existingLocationIds = $upstream->locations->pluck('id')->toArray();
        $newLocationIds = [];

        foreach ($request->locations as $locationData) {
            if (!empty($locationData['location'])) {
    
                // Update existing locations or create new ones
                $upstreamLocation = UpstreamLocation::updateOrCreate(
                    [
                        'upstream_id' => $upstream->id,
                        'location' => $locationData['location']
                    ],
                    [
                        'tls' => $locationData['tls']
                    ]
                );
                $newLocationIds[] = $upstreamLocation->id;
            }
        }

        // Delete locations that are not in the new list
        UpstreamLocation::where('upstream_id', $upstream->id)
            ->whereNotIn('id', $newLocationIds)
            ->delete();

        return redirect()->route('upstream.list', $upstream->rustProxy)->with('success', 'Upstream updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Upstream $upstream)
    {
        $upstream->delete();

        $defaultApp = RustProxySetting::where('key', 'default_app')->value('value');
        if ($upstream->rustProxy->upstreams()->count() == 0 && $defaultApp == $upstream->rustProxy->id) {
            RustProxySetting::where('key', 'default_app')->update(['value' => '0']);
            $additional_success_msg = ' No more upstreams available to this proxy. Resetting default proxy setting.';
        }

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('upstream.list', $upstream->rustProxy)->with('success', 'Upstream deleted successfully.' . ($additional_success_msg ?? ''));
    }

}
