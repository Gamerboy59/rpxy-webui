<?php

namespace App\Services;

use App\Models\RustProxy;
use App\Models\RustProxySetting;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\ConfigSaveException;

class ConfigGenerator
{
    public static function generateAndSaveConfig()
    {
        // Retrieve path to config from settings
        $configFilePath = RustProxySetting::where('key', 'config_file_path')->value('value');

        // Generate config
        $configContent = self::generateConfigContent();

        // Save config
        if (!Storage::put($configFilePath, $configContent)) {
            return false;
        }
        return true;
    }

    protected static function generateConfigContent()
    {
        $settings = RustProxySetting::all()->groupBy('section');
        $rustProxies = RustProxy::with('upstreams')->get();

        $configContent = "###################################\n";
        $configContent .= "#  DO NOT TOUCH: AUTO GENERATED   #\n";
        $configContent .= "###################################\n";

        // Settings
        foreach ($settings as $section => $sectionSettings) {
            $configContent .= "### Settings from RustProxySetting\n";
            if ($section !== 'general') {
                $configContent .= "[$section]\n";
            }
            foreach ($sectionSettings as $setting) {
                if($setting->type == 'checkbox'){
                    $setting->value = ($setting->key == '1') ? 'true' : 'false';
                }
                if ($section == 'general') {
                    if($setting->key == 'default_application'){
                        $defaultRustProxy = RustProxy::find($setting->value);
                        $setting->value = $defaultRustProxy ? $defaultRustProxy->app_name : 'none';
                    }
                    if($setting->key !== 'config_file_path'){
                        $configContent .= "{$setting->key} = " . ($setting->type == 'text' ? '"' : '') . "{$setting->value}" . ($setting->type == 'text' ? '"' : '') . "\n";
                    }
                } else {
                    $configContent .= "{$setting->key} = " . ($setting->type == 'text' ? '"' : '') . "{$setting->value}" . ($setting->type == 'text' ? '"' : '') . "\n";
                }
            }
        }

        $configContent .= "\n### Each RustProxy as apps.<rustproxy app_name>\n";
        $configContent .= "[apps]\n";

        foreach ($rustProxies as $rustProxy) {
            $configContent .= "\n### {$rustProxy->app_name} ###\n";
            $configContent .= "[apps.{$rustProxy->app_name}]\n";
            $configContent .= "server_name = '{$rustProxy->server_name}'\n";
            $configContent .= "tls = { https_redirection = " . ($rustProxy->https_redirection ? 'true' : 'false') . ", tls_cert_path = '{$rustProxy->tls_cert_path}', tls_cert_key_path = '{$rustProxy->tls_cert_key_path}'";
            if ($rustProxy->client_ca_cert_path) {
                $configContent .= ", client_ca_cert_path = '{$rustProxy->client_ca_cert_path}'";
            }
            $configContent .= " }\n";

            foreach ($rustProxy->upstreams as $upstream) {
                $configContent .= "\n[[apps.{$rustProxy->app_name}.reverse_proxy]]\n";
                if ($upstream->path && $upstream->replace_path) {
                    $configContent .= "path = '{$upstream->path}'\n";
                    $configContent .= "replace_path = '{$upstream->replace_path}'\n";
                }
                $configContent .= "upstream = [\n";
                foreach ($upstream->locations as $location) {
                    $configContent .= "  { location = '{$location->location}', tls = " . ($location->tls ? 'true' : 'false') . " },\n";
                }
                $configContent .= "]\n";
                $configContent .= "load_balance = \"{$upstream->loadbalanceType->name}\"\n";
                $configContent .= "upstream_options = [\n";
                foreach ($upstream->upstreamOptions as $option) {
                    $configContent .= "  \"{$option->option}\",\n";
                }
                $configContent .= "]\n";
            }
        }

        return $configContent;
    }
}
