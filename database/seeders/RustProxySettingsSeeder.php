<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RustProxySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('rust_proxy_settings')->insert([
            [
                'section' => 'general',
                'key' => 'config_file_path',
                'value' => '/home/rpxy/config.toml',
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
                'key' => 'default_application',
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
                'value' => './cache',
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
            ]
        ]);
    }
}
