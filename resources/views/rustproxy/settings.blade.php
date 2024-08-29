@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Proxy Settings</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('rustproxysettings.update') }}">
        @csrf
        @method('PUT')

        @foreach($sections as $section => $settings)
            <div class="card mb-4">
                <div class="card-header">
                    {{ ucwords(str_replace('h3', 'HTTP/3', str_replace('.', ' ', $section))) }} Settings
                </div>
                <div class="card-body">

                @foreach($settings as $index => $setting)
                    @php
                        $isLast = $index === count($settings) - 1;
                    @endphp
                    {!! $isLast ? '' : '<div class="mb-3">' !!}
                        
                    @if($setting->type == 'text' || $setting->type == 'number')
                        <label for="setting-{{ $setting->key }}" class="form-label">{{ $setting->key }}</label>
                        @if($setting->key == "config_file_path")
                        <div class="input-group">
                            <span class="input-group-text" id="laravel-default-path">{{ storage_path('app') }}&sol;</span>
                            <input type="{{ $setting->type }}" class="form-control @error('settings.' . $setting->key . '.value') is-invalid @enderror" id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]" value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}" aria-describedby="laravel-default-path">
                        </div>
                        <p class="ms-1 my-2 text-muted">Start rpxy with: <code>./target/release/rpxy -w -c {{ storage_path('app') }}/{{ old('settings.' . $setting->key . '.value', $setting->value) }}</code></p>
                        @else
                            <input type="{{ $setting->type }}" class="form-control @error('settings.' . $setting->key . '.value') is-invalid @enderror" id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]" value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}">
                        @endif
                        @error('settings.' . $setting->key . '.value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @elseif($setting->type == 'checkbox')
                        <div class="form-check">
                            <label for="setting-{{ $setting->key }}" class="form-label">{{ $setting->key }}</label>
                            <input type="hidden" name="settings[{{ $setting->key }}][value]" value="0">
                            <input type="checkbox" class="form-check-input @error('settings.' . $setting->key . '.value') is-invalid @enderror" id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]" value="1" {{ old('settings.' . $setting->key . '.value', $setting->value) ? 'checked' : '' }}>
                            @error('settings.' . $setting->key . '.value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif($setting->type == 'select')
                        <label for="setting-{{ $setting->key }}" class="form-label">{{ $setting->key }}</label>
                        <select class="form-control @error('settings.' . $setting->key . '.value') is-invalid @enderror" id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]">
                            <option value="0" {{ old('settings.' . $setting->key . '.value', $setting->value) == 0 ? 'selected' : '' }} disabled>None</option>
                            @if($setting->key == 'default_app')    
                                @foreach($validRustProxies as $rustProxy)
                                    <option value="{{ $rustProxy->id }}" {{ old('settings.' . $setting->key . '.value', $setting->value) == $rustProxy->id ? 'selected' : '' }}>
                                        {{ $rustProxy->server_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('settings.' . $setting->key . '.value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif

                        <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                        <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                    {!! $isLast ? '' : '</div>' !!}
                @endforeach

                </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
@endsection
