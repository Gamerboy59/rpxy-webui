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
                    <input class="form-check-input" type="checkbox" id="https_redirection" name="https_redirection" value="1" {{ old('https_redirection', $rustProxy->https_redirection) ? 'checked' : '' }} {{ old('ssl_enabled', $rustProxy->ssl_enabled) ? '' : 'disabled' }}>
                    <label class="form-check-label" for="https_redirection">
                        HTTPS Redirection
                    </label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="letsencrypt_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="letsencrypt_enabled" name="letsencrypt_enabled" value="1" {{ old('letsencrypt_enabled', $rustProxy->letsencrypt_enabled) ? 'checked' : '' }} {{ old('ssl_enabled', $rustProxy->ssl_enabled) ? '' : 'disabled' }}>
                    <label class="form-check-label" for="letsencrypt_enabled" data-bs-toggle="tooltip" data-bs-placement="top" title="Using Let's Encrypt your certificates will be managed and renewed automatically.">Let's Encrypt</label>
                </div>

                <div class="row {{ old('ssl_enabled', $rustProxy->ssl_enabled) && !old('letsencrypt_enabled', $rustProxy->letsencrypt_enabled) ? '' : 'd-none' }}" id="certificates">
                    <div class="col-md-4 mb-3">
                        <label for="tls_cert" class="form-label">TLS Certificate</label>
                        <textarea class="form-control @error('tls_cert') is-invalid @enderror" id="tls_cert" name="tls_cert" {{ old('ssl_enabled', $rustProxy->ssl_enabled) && !old('letsencrypt_enabled', $rustProxy->letsencrypt_enabled) ? '' : 'disabled' }}>{{ old('tls_cert', $tlsCertContent) }}</textarea>
                        @error('tls_cert')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tls_cert_key" class="form-label">TLS Certificate Key</label>
                        <textarea class="form-control @error('tls_cert_key') is-invalid @enderror" id="tls_cert_key" name="tls_cert_key" {{ old('ssl_enabled', $rustProxy->ssl_enabled) && !old('letsencrypt_enabled', $rustProxy->letsencrypt_enabled) ? '' : 'disabled' }}>{{ old('tls_cert_key', $tlsCertKeyContent) }}</textarea>
                        @error('tls_cert_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="client_ca_cert" class="form-label">Client CA Certificate</label>
                        <textarea class="form-control @error('client_ca_cert') is-invalid @enderror" id="client_ca_cert" name="client_ca_cert" aria-describedby="ClientCAoptional" {{ old('ssl_enabled', $rustProxy->ssl_enabled) && !old('letsencrypt_enabled', $rustProxy->letsencrypt_enabled) ? '' : 'disabled' }}>{{ old('client_ca_cert', $clientCaCertContent) }}</textarea>
                        <div id="ClientCAoptional" class="form-text">
                            optional
                        </div>
                        @error('client_ca_cert')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
<script>
    const sslEnabledCheckbox = document.getElementById('ssl_enabled');
    const httpsRedirectionCheckbox = document.getElementById('https_redirection');
    const letsEncryptCheckbox = document.getElementById('letsencrypt_enabled');
    const certificatesDiv = document.getElementById('certificates');

    function disableCertFields() {
        const isSSLEnabled = sslEnabledCheckbox.checked;
        httpsRedirectionCheckbox.disabled = !isSSLEnabled;
        letsEncryptCheckbox.disabled = !isSSLEnabled;

        if (isSSLEnabled && !letsEncryptCheckbox.checked) {
            certificatesDiv.classList.remove('d-none');
            certificatesDiv.querySelectorAll('textarea').forEach(field => field.disabled = false);
        } else {
            certificatesDiv.querySelectorAll('textarea').forEach(field => field.disabled = true);
        }
    }

    function hideCertFields() {
        const isSSLEnabled = sslEnabledCheckbox.checked;

        if (isSSLEnabled && !letsEncryptCheckbox.checked) {
            certificatesDiv.classList.remove('d-none');
            certificatesDiv.querySelectorAll('textarea').forEach(field => field.disabled = false);
        } else {
            certificatesDiv.classList.add('d-none');
        }
    }

    sslEnabledCheckbox.addEventListener('change', disableCertFields);
    letsEncryptCheckbox.addEventListener('change', hideCertFields);
</script>
@endsection
