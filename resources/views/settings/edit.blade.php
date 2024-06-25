@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Settings</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Profile Card -->
        <div class="card mb-3">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" placeholder="Enter your name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" placeholder="Enter a new email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter a new password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password">
                    </div>
                    <div class="col-md-4 mt-auto mb-lg-3">
                        <span id="passwordHelpInline" class="form-text">
                            Must be at least 8 characters long.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Card -->
        <div class="card mb-4">
            <div class="card-header">Settings</div>
            <div class="card-body">
                @foreach($settings as $index => $setting)
                    @php
                        $isLast = $index === count($settings) - 1;
                    @endphp
                    {!! $isLast ? '' : '<div class="mb-3">' !!}
                        
                    @if($setting->type == 'text' || $setting->type == 'number')
                        <label for="setting-{{ $setting->key }}" class="form-label">{{ $setting->key }}</label>
                        <input type="{{ $setting->type }}" class="form-control @error('settings.' . $setting->key . '.value') is-invalid @enderror" id="setting-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]" value="{{ old('settings.' . $setting->key . '.value', $setting->value) }}">
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
                    @endif

                        <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                        <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                        {!! $isLast ? '' : '</div>' !!}
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
