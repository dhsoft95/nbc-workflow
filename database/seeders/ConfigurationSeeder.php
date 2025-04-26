<?php

namespace Database\Seeders;

use App\Models\ConfigurationCategory;
use App\Models\ConfigurationItem;
use App\Models\SlaConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Configuration Categories
        $middlewareCategory = ConfigurationCategory::create([
            'name' => 'Middleware Connections',
            'key' => 'middleware_connections',
            'description' => 'Options for middleware connection types',
        ]);

        $securityClassCategory = ConfigurationCategory::create([
            'name' => 'Security Classifications',
            'key' => 'security_classifications',
            'description' => 'Security classification levels',
        ]);

        $responsibleTeamCategory = ConfigurationCategory::create([
            'name' => 'Responsible Teams',
            'key' => 'responsible_teams',
            'description' => 'Teams responsible for implementation',
        ]);

        $connectionMethodsCategory = ConfigurationCategory::create([
            'name' => 'Connection Methods',
            'key' => 'connection_methods',
            'description' => 'External integration connection methods',
        ]);

        $authMethodsCategory = ConfigurationCategory::create([
            'name' => 'Authentication Methods',
            'key' => 'authentication_methods',
            'description' => 'Available authentication methods',
        ]);

        $dataFormatsCategory = ConfigurationCategory::create([
            'name' => 'Data Formats',
            'key' => 'data_formats',
            'description' => 'Supported data formats',
        ]);

        $featuresCategory = ConfigurationCategory::create([
            'name' => 'Supported Features',
            'key' => 'supported_features',
            'description' => 'Features that can be supported by integration',
        ]);

        // Add Configuration Items

        // Middleware connections
        $middlewareOptions = [
            'API Gateway',
            'ESB',
            'MuleSoft',
            'Apache Camel',
            'Direct Database',
            'Message Queue',
            'WebSockets',
            'GraphQL',
        ];

        foreach ($middlewareOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $middlewareCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Security classifications
        $securityOptions = [
            'Public',
            'Internal',
            'Confidential',
            'Restricted',
            'Highly Restricted',
        ];

        foreach ($securityOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $securityClassCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Responsible teams
        $teamOptions = [
            'Digital Team',
            'IDI Team',
            'Third-party Vendor',
            'External Consultant',
        ];

        foreach ($teamOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $responsibleTeamCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Connection methods
        $connectionOptions = [
            'VPN',
            'Internet',
            'Direct Connect',
            'API Gateway',
            'SFTP',
            'HTTPS',
        ];

        foreach ($connectionOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $connectionMethodsCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Authentication methods
        $authOptions = [
            'OAuth 2.0',
            'API Key',
            'JWT',
            'Certificate',
            'Basic Auth',
            'SAML',
            'OpenID Connect',
        ];

        foreach ($authOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $authMethodsCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Data formats
        $formatOptions = [
            'JSON',
            'XML',
            'CSV',
            'YAML',
            'Protocol Buffers',
            'Avro',
            'Parquet',
        ];

        foreach ($formatOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $dataFormatsCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }

        // Features
        $featureOptions = [
            'Data Exchange',
            'Real-time Sync',
            'Batch Processing',
            'Event Triggering',
            'Transformation',
            'Validation',
            'Error Handling',
            'Monitoring',
            'Logging',
        ];

        foreach ($featureOptions as $index => $option) {
            ConfigurationItem::create([
                'category_id' => $featuresCategory->id,
                'name' => $option,
                'value' => strtolower(str_replace(' ', '_', $option)),
                'is_active' => true,
                'display_order' => $index,
            ]);
        }
        DB::table('sla_configurations')->delete();
        // Set up SLA configurations
        $slaStages = [
            'app_owner' => [24, 48],
            'idi' => [48, 72],
            'security' => [48, 96],
            'infrastructure' => [24, 48],
        ];

        foreach ($slaStages as $stage => $hours) {
            SlaConfiguration::updateOrCreate([
                'stage' => $stage,
                'warning_hours' => $hours[0],
                'critical_hours' => $hours[1],
                'include_weekends' => false,
            ]);
        }
    }
}
