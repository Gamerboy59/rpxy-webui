@extends('layouts.app')

@section('content')
<div class="container">
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

    <div class="card">
        <div class="card-header">
            Edit Proxy
        </div>
        <div class="card-body">
            <form action="{{ route('proxy.update', $rustProxy->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="app_name" class="form-label">App Name</label>
                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $rustProxy->app_name) }}" required>
                    @error('app_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="server_name" class="form-label">Server Name</label>
                    <input type="text" class="form-control @error('server_name') is-invalid @enderror" id="server_name" name="server_name" value="{{ old('server_name', $rustProxy->server_name) }}" required>
                    @error('server_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="ssl_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="ssl_enabled" name="ssl_enabled" value="1" {{ old('ssl_enabled', $rustProxy->ssl_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="ssl_enabled">
                        SSL Enabled
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="https_redirection" value="0">
                    <input class="form-check-input" type="checkbox" id="https_redirection" name="https_redirection" value="1" {{ old('https_redirection', $rustProxy->https_redirection) ? 'checked' : '' }}>
                    <label class="form-check-label" for="https_redirection">
                        HTTPS Redirection
                    </label>
                </div>
                <div class="mb-3">
                    <label for="tls_cert_path" class="form-label">TLS Cert Path</label>
                    <input type="text" class="form-control @error('tls_cert_path') is-invalid @enderror" id="tls_cert_path" name="tls_cert_path" value="{{ old('tls_cert_path', $rustProxy->tls_cert_path) }}">
                    @error('tls_cert_path')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="tls_cert_key_path" class="form-label">TLS Cert Key Path</label>
                    <input type="text" class="form-control @error('tls_cert_key_path') is-invalid @enderror" id="tls_cert_key_path" name="tls_cert_key_path" value="{{ old('tls_cert_key_path', $rustProxy->tls_cert_key_path) }}">
                    @error('tls_cert_key_path')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="client_ca_cert_path" class="form-label">Client CA Cert Path</label>
                    <input type="text" class="form-control @error('client_ca_cert_path') is-invalid @enderror" id="client_ca_cert_path" name="client_ca_cert_path" value="{{ old('client_ca_cert_path', $rustProxy->client_ca_cert_path) }}">
                    @error('client_ca_cert_path')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
