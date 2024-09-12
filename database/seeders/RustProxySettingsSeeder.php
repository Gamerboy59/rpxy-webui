<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RustProxySetting;

class RustProxySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'section' => 'general',
                'key' => 'config_file_path',
                'value' => 'config.toml',
                'type' => 'text'
            ],
            [
                'section' => 'general',
                'key' => 'listen_port',
                'value' => '80',
                'type' => 'number'
            ],
            [
                'section' => 'general',
                'key' => 'listen_port_tls',
                'value' => '443',
                'type' => 'number'
            ],
            [
                'section' => 'general',
                'key' => 'default_app',
                'value' => '0',
                'type' => 'select'
            ],
            [
                'section' => 'general',
                'key' => 'tcp_listen_backlog',
                'value' => '1024',
                'type' => 'number'
            ],
            [
                'section' => 'general',
                'key' => 'max_concurrent_streams',
                'value' => '100',
                'type' => 'number'
            ],
            [
                'section' => 'general',
                'key' => 'max_clients',
                'value' => '512',
                'type' => 'number'
            ],
            [
                'section' => 'general',
                'key' => 'listen_ipv6',
                'value' => '1',
                'type' => 'checkbox'
            ],
            [
                'section' => 'experimental',
                'key' => 'ignore_sni_consistency',
                'value' => '0',
                'type' => 'checkbox'
            ],
            [
                'section' => 'experimental',
                'key' => 'connection_handling_timeout',
                'value' => '0',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'alt_svc_max_age',
                'value' => '3600',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'request_max_body_size',
                'value' => '65536',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'max_concurrent_connections',
                'value' => '10000',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'max_concurrent_bidistream',
                'value' => '100',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'max_concurrent_unistream',
                'value' => '100',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.h3',
                'key' => 'max_idle_timeout',
                'value' => '10',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.cache',
                'key' => 'cache_dir',
                'value' => (is_dir('/run/rpxy/') ? '/run/rpxy/' : (is_dir($d = rtrim(shell_exec('find /tmp/systemd-private-*/tmp -type d -name "rpxy" 2>/dev/null | head -n 1'))) ? "$d/" : '/tmp/rpxy/')) . '.cache',
                'type' => 'text'
            ],
            [
                'section' => 'experimental.cache',
                'key' => 'max_cache_entry',
                'value' => '1000',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.cache',
                'key' => 'max_cache_each_size',
                'value' => '65535',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.cache',
                'key' => 'max_cache_each_size_on_memory',
                'value' => '4096',
                'type' => 'number'
            ],
            [
                'section' => 'experimental.acme',
                'key' => 'dir_url',
                'value' => 'https://acme-v02.api.letsencrypt.org/directory',
                'type' => 'text'
            ],
            [
                'section' => 'experimental.acme',
                'key' => 'email',
                'value' => 'admin@' . (preg_match("/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/", gethostname()) ? gethostname() : 'example.com'),
                'type' => 'text'
            ],
            [
                'section' => 'experimental.acme',
                'key' => 'registry_path',
                'value' => (is_dir('/etc/rpxy/') ? '/etc/rpxy/' : '.') . 'acme_registry',
                'type' => 'text'
            ]
        ];

        foreach ($settings as $setting) {
            RustProxySetting::firstOrCreate(
                ['section' => $setting['section'], 'key' => $setting['key']], // Search criteria
                ['value' => $setting['value'], 'type' => $setting['type']] // // Updateable value or create new dataset of them including key
            );
        }
    }
}
