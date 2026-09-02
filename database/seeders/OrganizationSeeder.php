<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationBranding;
use App\Models\OrganizationUnit;
use App\Models\ReminderPolicy;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds NexCore Systems as Kandarasi's first tenant -- dogfooding the
 * product on the company building it, rather than a placeholder org.
 * Branding pulled from NexCore's existing design system so white-label
 * branding is exercised from day one, not left untested until a real
 * customer sets their own colors.
 */
class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $nexcore = Organization::create([
            'name' => 'NexCore Systems',
            'slug' => 'nexcore',
            'type' => 'company',
            'is_active' => true,
        ]);

        OrganizationBranding::create([
            'organization_id' => $nexcore->id,
            'primary_color' => '#0C0910',   // obsidian
            'secondary_color' => '#F0521E', // volcanic orange
            'accent_color' => '#00E5A0',    // electric mint
        ]);

        Subscription::create([
            'organization_id' => $nexcore->id,
            'plan' => 'internal',
            'seat_limit' => 25,
            'status' => 'active',
        ]);

        // A handful of departments matching the v1 dashboard's breakdown,
        // now proper organization_units rows instead of a loose string.
        foreach (['Engineering', 'Operations', 'Finance', 'Procurement'] as $unitName) {
            OrganizationUnit::create([
                'organization_id' => $nexcore->id,
                'name' => $unitName,
            ]);
        }

        $admin = User::create([
            'organization_id' => $nexcore->id,
            'name' => 'Kevin Musanii',
            'email' => 'admin@nexcore.systems',
            'password' => Hash::make('change-me-immediately'),
            'role' => 'org_admin',
            'is_active' => true,
        ]);

        // Org-level default reminder policy (contract_id = null) --
        // 90/30/7 days before expiry, email + SMS, no digest.
        ReminderPolicy::create([
            'organization_id' => $nexcore->id,
            'contract_id' => null,
            'offsets_days' => [90, 30, 7],
            'channels' => ['email', 'sms'],
            'digest' => false,
        ]);

        $this->command?->info("Seeded NexCore Systems (slug: nexcore). Admin login: {$admin->email} / change-me-immediately");
    }
}
