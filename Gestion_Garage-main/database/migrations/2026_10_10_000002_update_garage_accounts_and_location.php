<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $accounts = [
        ['username' => 'admin', 'role' => 'admin', 'email' => 'nirbhaoguetaba@gmail.com'],
        ['username' => 'marc.dupont', 'role' => 'technicien', 'email' => 'jeanguetaba08@gmail.com'],
        ['username' => 'sophie.martin', 'role' => 'client', 'email' => 'guetabajoachim@gmail.com'],
    ];

    public function up(): void
    {
        foreach ($this->accounts as $account) {
            $emailInUse = DB::table('users')
                ->where('email', $account['email'])
                ->where('username', '!=', $account['username'])
                ->exists();

            if ($emailInUse) {
                throw new \RuntimeException("The configured garage login email {$account['email']} is already assigned to another account.");
            }
        }

        DB::transaction(function (): void {
            foreach ($this->accounts as $account) {
                $updates = [
                    'email' => $account['email'],
                    'updated_at' => now(),
                ];

                if ($account['role'] !== 'client') {
                    $updates['Adresse'] = 'Lomé, Togo';
                }

                DB::table('users')
                    ->where('username', $account['username'])
                    ->where('role', $account['role'])
                    ->update($updates);
            }

            DB::table('users')
                ->where('role', 'technicien')
                ->whereIn('username', ['marc.dupont', 'samir.benali'])
                ->update([
                    'Adresse' => 'Lomé, Togo',
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            DB::table('users')
                ->where('username', 'admin')
                ->where('role', 'admin')
                ->update([
                    'email' => 'admin@garage.com',
                    'Adresse' => '148 Avenue des Garages, 75011 Paris',
                    'updated_at' => now(),
                ]);

            DB::table('users')
                ->where('username', 'marc.dupont')
                ->where('role', 'technicien')
                ->update([
                    'email' => 'mecanicien@garage.com',
                    'Adresse' => '12 Rue des Ateliers, Paris',
                    'updated_at' => now(),
                ]);

            DB::table('users')
                ->where('username', 'sophie.martin')
                ->where('role', 'client')
                ->update([
                    'email' => 'client@garage.com',
                    'updated_at' => now(),
                ]);

            DB::table('users')
                ->where('username', 'samir.benali')
                ->where('role', 'technicien')
                ->update([
                    'Adresse' => '24 Boulevard Voltaire, Paris',
                    'updated_at' => now(),
                ]);
        });
    }
};
