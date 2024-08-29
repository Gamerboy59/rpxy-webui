<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        $defaultApp = RustProxySetting::where('key', 'default_app')->value('value');
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
            'letsencrypt_enabled' => 'nullable|boolean|required_if:ssl_enabled,true',
            'tls_cert' => [
                'max:8192',
                function ($attribute, $value, $fail) use ($request) {
                    // If SSL is disabled or Let's Encrypt is activated, allow empty values.
                    if (!$request->ssl_enabled || $request->letsencrypt_enabled) {
                        return true;
                    }
        
                    // If SSL is enabled and Let's Encrypt is deactivated, do not allow empty values.
                    if (empty($value)) {
                        $fail('The TLS Certificate is required when SSL is enabled and Let\'s Encrypt is disabled.');
                        return false;
                    }

                    // Make sure it is a valid string because only this can be processed by openssl
                    if (!is_string($value)) {
                        $fail('The TLS Certificate field contains illegal characters.');
                        return false;
                    }

                    return $this->validateCertificate($value, $fail);
                }
            ],
            'tls_cert_key' => [
                'max:8192',
                function ($attribute, $value, $fail) use ($request) {
                    // If SSL is disabled or Let's Encrypt is activated, allow empty values.
                    if (!$request->ssl_enabled || $request->letsencrypt_enabled) {
                        return true;
                    }

                    // If SSL is enabled and Let's Encrypt is deactivated, do not allow empty values.
                    if (empty($value)) {
                        $fail('The TLS Private Key is required when SSL is enabled and Let\'s Encrypt is disabled.');
                        return false;
                    }

                    // Make sure it is a valid string because only this can be processed by openssl
                    if (!is_string($value)) {
                        $fail('The TLS Certificate field contains illegal characters.');
                        return false;
                    }

                    return $this->validatePrivateKey($value, $request, $fail);
                }
            ],
            'client_ca_cert' => [
                'nullable',
                'string',
                'max:8192',
                function ($attribute, $value, $fail) {
                    return $this->validateCertificate($value, $fail);
                },
            ],
        ]);

        $rustProxy = RustProxy::create($request->only(['app_name', 'server_name', 'ssl_enabled', 'https_redirection', 'letsencrypt_enabled']));

        foreach(['tls_cert', 'tls_cert_key', 'client_ca_cert'] as $fieldName) {
            if (!$this->storeCertificateContent($request, $rustProxy, $fieldName)) {
                return redirect()->back()->with('error', 'Failed to save the certificate file.');
            }
        }

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
        $tlsCertContent = $this->getCertificateContent($rustProxy, 'tls_cert');
        $tlsCertKeyContent = $this->getCertificateContent($rustProxy, 'tls_cert_key');
        $clientCaCertContent = $this->getCertificateContent($rustProxy, 'client_ca_cert');

        return view('rustproxy.edit', compact('rustProxy', 'tlsCertContent', 'tlsCertKeyContent', 'clientCaCertContent'));
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
            'letsencrypt_enabled' => 'nullable|boolean|required_if:ssl_enabled,true',
            'tls_cert' => [
                'max:8192',
                function ($attribute, $value, $fail) use ($request) {
                    // If SSL is disabled or Let's Encrypt is activated, allow empty values.
                    if (!$request->ssl_enabled || $request->letsencrypt_enabled) {
                        return true;
                    }
        
                    // If SSL is enabled and Let's Encrypt is deactivated, do not allow empty values.
                    if (empty($value)) {
                        $fail('The TLS Certificate is required when SSL is enabled and Let\'s Encrypt is disabled.');
                        return false;
                    }

                    // Make sure it is a valid string because only this can be processed by openssl
                    if (!is_string($value)) {
                        $fail('The TLS Certificate field contains illegal characters.');
                        return false;
                    }

                    return $this->validateCertificate($value, $fail);
                }
            ],
            'tls_cert_key' => [
                'max:8192',
                function ($attribute, $value, $fail) use ($request) {
                    // If SSL is disabled or Let's Encrypt is activated, allow empty values.
                    if (!$request->ssl_enabled || $request->letsencrypt_enabled) {
                        return true;
                    }

                    // If SSL is enabled and Let's Encrypt is deactivated, do not allow empty values.
                    if (empty($value)) {
                        $fail('The TLS Private Key is required when SSL is enabled and Let\'s Encrypt is disabled.');
                        return false;
                    }

                    // Make sure it is a valid string because only this can be processed by openssl
                    if (!is_string($value)) {
                        $fail('The TLS Certificate field contains illegal characters.');
                        return false;
                    }

                    return $this->validatePrivateKey($value, $request, $fail);
                }
            ],
            'client_ca_cert' => [
                'nullable',
                'string',
                'max:8192',
                function ($attribute, $value, $fail) {
                    return $this->validateCertificate($value, $fail);
                }
            ],
        ]);

        $rustProxy->update($request->only(['app_name', 'server_name', 'ssl_enabled', 'https_redirection', 'letsencrypt_enabled']));

        foreach(['tls_cert', 'tls_cert_key', 'client_ca_cert'] as $fieldName) {
            if (!$this->storeCertificateContent($request, $rustProxy, $fieldName)) {
                return redirect()->back()->with('error', 'Failed to save the certificate file.');
            }
        }

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
        $defaultApp = RustProxySetting::where('key', 'default_app')->value('value');
        if ($defaultApp == $rustProxy->id) {
            RustProxySetting::where('key', 'default_app')->update(['value' => '0']);
            $additional_success_msg = ' Resetting default proxy setting.';
        }

        $rustProxy->upstreams()->delete();
        $rustProxy->delete();

        if (!ConfigGenerator::generateAndSaveConfig()) {
            return redirect()->back()->with('error', 'Failed to save the configuration file.');
        }

        return redirect()->route('proxy.index')->with('success', 'Proxy deleted successfully.' . ($additional_success_msg ?? ''));
    }

    protected function storeCertificateContent(Request $request, RustProxy $rustProxy, $fieldName)
    {
        $folderPath = "certificates/{$rustProxy->server_name}";
        $filePath = "{$folderPath}/{$fieldName}.pem";
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, null, true);
        }
        if(Storage::put($filePath, $request->input($fieldName) ?? "\n")) {
            // Save path of created or updated cert file in database
            $rustProxy->update([$fieldName . '_path' => $filePath]);
            return true;
        } else {
            return false;
        }
    }

    protected function getCertificateContent(RustProxy $rustProxy, $fieldName)
    {
        $filePath = $rustProxy->{$fieldName . '_path'};
        return $filePath && Storage::exists($filePath) ? Storage::get($filePath) : '';
    }

    protected function validateCertificate($certInput, $fail)
    {
        if (extension_loaded('openssl')) {
            $certResource = @openssl_x509_read($certInput);

            if ($certResource) {
                openssl_x509_free($certResource);
                return true;
            } else {
                $fail('The Client CA Certificate is not a valid certificate.');
                return false;
            }
        }
    }

    protected function validatePrivateKey($pkeyInput, Request $request, $fail)
    {
        if (extension_loaded('openssl')) {
            $certResource = @openssl_x509_read($request->tls_cert);
            $pkeyResource = @openssl_pkey_get_private($pkeyInput);
            if (!$pkeyResource) {
                $fail('The TLS Certificate Key is not a valid private key.');
                return false;
            }
            if ($certResource && $pkeyResource && !openssl_x509_check_private_key($certResource, $pkeyResource)) {
                $fail('The TLS Certificate Key does not match the Certificate.');
                return false;
            }

            $details = openssl_pkey_get_details($pkeyResource);
            // Check if PKCS8 formatted key
            if (strpos($details['key'], 'BEGIN PRIVATE KEY') === false) {
                // If not, convert to PKCS8
                $pkcs8Key = '';
                if (openssl_pkey_export($pkeyResource, $pkcs8Key, null, ['output' => 'PKCS8'])) {
                    $request->merge(['tls_cert_key' => $pkcs8Key]);
                } else {
                    $fail('Failed to convert the TLS Certificate Key to PKCS8 format.');
                    return false;
                }
            }

            if ($certResource) {
                openssl_x509_free($certResource);
                return true;
            }
            if ($pkeyResource) {
                openssl_pkey_free($pkeyResource);
                return true;
            }
        }
    }

}
