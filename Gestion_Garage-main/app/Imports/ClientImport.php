<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'Nom' => $row['Nom'] ?? null,
            'Prenom' => $row['Prenom'] ?? null,
            'username' => $row['UserName'] ?? null,
            'Adresse' => $row['Adresse'] ?? null,
            'email' => $row['Email'] ?? null,
            'NumeroTelephone' => $row['Telephone'] ?? null,
            'role' => $row['Role'] ?? 'client',
        ]);
    }
}

if (!class_exists('App\Imports\client')) {
    class_alias(ClientImport::class, 'App\Imports\client');
}

