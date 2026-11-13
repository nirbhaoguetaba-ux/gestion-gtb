<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Mecanique;
use App\Models\Reparation;
use App\Models\Vehicule;
use App\Models\VehiculeOccasion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Mail\VehiculePretMail;
use Database\Seeders\VehiculeOccasionSeeder;
use Tests\TestCase;

class AuthenticationAndMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_workspace_and_the_used_car_catalog_require_authentication(): void
    {
        foreach ([
            '/admin/dashboard',
            '/technicien/dashboard',
            '/client/dashboard',
            '/vehicules-occasion',
        ] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }

        $this->get('/demo/preview-mail/1')->assertNotFound();
        $this->get('/login')->assertOk()->assertDontSee('admin@garage.com');
    }

    public function test_a_client_cannot_open_the_administrator_workspace(): void
    {
        $client = $this->createUser('client');

        $this->actingAs($client)
            ->get('/admin/dashboard')
            ->assertRedirect(route('client.dashboard'));
    }

    public function test_unrecognized_roles_are_denied_instead_of_redirected_between_workspaces(): void
    {
        $user = $this->createUser('unknown');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_authenticated_users_can_view_available_used_cars(): void
    {
        $client = $this->createUser('client');

        VehiculeOccasion::create([
            'reference' => 'VO-TEST-001',
            'marque' => 'Peugeot',
            'modele' => '208',
            'annee' => 2022,
            'kilometrage' => 12000,
            'carburant' => 'Essence',
            'boite' => 'Manuelle',
            'prix' => 15000,
            'image_url' => 'https://images.unsplash.com/test.jpg',
            'description' => 'Véhicule de test.',
            'statut' => 'disponible',
        ]);

        $this->actingAs($client)
            ->get('/vehicules-occasion')
            ->assertOk()
            ->assertSee('VO-TEST-001')
            ->assertSee('Peugeot 208');
    }

    public function test_only_administrators_can_reduce_a_used_car_price(): void
    {
        $vehicule = VehiculeOccasion::create([
            'reference' => 'VO-PRICE-001',
            'marque' => 'Peugeot',
            'modele' => '208',
            'annee' => 2022,
            'kilometrage' => 12000,
            'carburant' => 'Essence',
            'boite' => 'Manuelle',
            'prix' => 15000000,
            'image_url' => 'https://images.unsplash.com/test.jpg',
            'description' => 'Véhicule de test.',
            'statut' => 'disponible',
        ]);

        $admin = $this->createUser('admin');
        $this->actingAs($admin)
            ->get(route('admin.vehicules-occasion'))
            ->assertOk()
            ->assertSee('Réduire le prix')
            ->assertDontSee('Contacter le garage');

        $this->put(route('admin.vehicules-occasion.price', $vehicule), ['prix' => 14000000])
            ->assertRedirect(route('admin.vehicules-occasion'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('vehicules_occasion', [
            'id' => $vehicule->id,
            'prix' => 14000000,
        ]);

        $this->from(route('admin.vehicules-occasion'))
            ->put(route('admin.vehicules-occasion.price', $vehicule), ['prix' => 15000000])
            ->assertSessionHasErrors('prix');
        $this->assertDatabaseHas('vehicules_occasion', [
            'id' => $vehicule->id,
            'prix' => 14000000,
        ]);

        $client = $this->createUser('client');
        $this->actingAs($client)
            ->get('/vehicules-occasion')
            ->assertOk()
            ->assertSee('Contacter le garage')
            ->assertDontSee('Réduire le prix');

        $this->put(route('admin.vehicules-occasion.price', $vehicule), ['prix' => 13000000])
            ->assertRedirect(route('client.dashboard'));
        $this->assertDatabaseHas('vehicules_occasion', [
            'id' => $vehicule->id,
            'prix' => 14000000,
        ]);
    }

    public function test_administrator_can_assign_a_client_and_upload_a_vehicle_photo(): void
    {
        Storage::fake('public');

        $admin = $this->createUser('admin');
        $client = $this->createUser('client');
        $clientModel = Client::create(['user_id' => $client->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('<span>Parc de véhicules</span>', false)
            ->assertSee(route('admin.vehicules') . '#addVehicleModal');

        $this->actingAs($admin)
            ->post(route('admin.vehicules.store'), [
                'client_id' => $clientModel->id,
                'brand' => 'Peugeot',
                'model' => '208',
                'annee' => 2022,
                'fuel_type' => 'Essence',
                'registration_number' => 'AA-123-AB',
                'kilometrage' => 12000,
                'boite_vitesse' => 'Manuelle',
                'couleur' => 'Bleu',
                'statut' => 'en_attente',
                'photo' => UploadedFile::fake()->image('peugeot-208.jpg'),
            ])
            ->assertRedirect(route('admin.vehicules'))
            ->assertSessionHas('success');

        $vehicule = Vehicule::where('registration_number', 'AA-123-AB')->firstOrFail();
        $this->assertSame($clientModel->id, $vehicule->client_id);
        $this->assertNotEmpty($vehicule->photo);
        Storage::disk('public')->assertExists($vehicule->photo);
    }

    public function test_registration_creates_a_client_account_and_logs_them_in(): void
    {
        $response = $this->post('/registration', [
            'Nom' => 'Martin',
            'Prenom' => 'Sophie',
            'email' => 'sophie@example.com',
            'NumeroTelephone' => '0102030405',
            'adresse' => '1 rue du Garage',
            'password' => 'un-mot-de-passe-solide',
            'password_confirmation' => 'un-mot-de-passe-solide',
        ]);

        $user = User::where('email', 'sophie@example.com')->firstOrFail();

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('client', $user->role);
        $this->assertSame('1 rue du Garage', $user->Adresse);
        $this->assertTrue(Hash::check('un-mot-de-passe-solide', $user->password));
        $this->assertDatabaseHas('clients', ['user_id' => $user->id]);
    }

    public function test_login_authenticates_by_email_and_redirects_to_the_users_role_workspace(): void
    {
        $admin = $this->createUser('admin');

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'motdepasse-long-test',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_seeded_catalog_contains_ten_used_cars(): void
    {
        $this->seed(VehiculeOccasionSeeder::class);

        $this->assertDatabaseCount('vehicules_occasion', 10);
    }

    public function test_technician_can_email_the_client_when_marking_an_assigned_repair_ready(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $technicien = $this->createUser('technicien');
        $mecanique = Mecanique::create([
            'user_id' => $technicien->id,
            'specialisation' => 'Mécanique générale',
        ]);
        $client = $this->createUser('client');
        $clientModel = Client::create(['user_id' => $client->id]);
        $vehicule = Vehicule::create([
            'client_id' => $clientModel->id,
            'brand' => 'Peugeot',
            'model' => '208',
            'annee' => 2022,
            'fuel_type' => 'Essence',
            'registration_number' => 'AA-123-AA',
            'kilometrage' => 12000,
            'boite_vitesse' => 'Manuelle',
            'couleur' => 'Bleu',
            'statut' => 'en_reparation',
        ]);
        $reparation = Reparation::create([
            'vehicule_id' => $vehicule->id,
            'mecanique_id' => $mecanique->id,
            'titre' => 'Révision',
            'status' => 'en_cours',
        ]);

        $autreTechnicien = $this->createUser('technicien', 'autre');
        Mecanique::create([
            'user_id' => $autreTechnicien->id,
            'specialisation' => 'Carrosserie',
        ]);

        $this->actingAs($autreTechnicien)
            ->put(route('technicien.reparations.updateStatus', $reparation), [
                'status' => 'prete',
            ])
            ->assertNotFound();

        $this->actingAs($technicien)
            ->put(route('technicien.reparations.updateStatus', $reparation), [
                'status' => 'prete',
                'notifier_client' => '1',
            ])
            ->assertSessionHas('success');

        Mail::assertSent(VehiculePretMail::class, function (VehiculePretMail $mail) use ($client) {
            return $mail->hasTo($client->email);
        });
        $this->assertDatabaseHas('reparations', [
            'id' => $reparation->id,
            'status' => 'prete',
            'notification_envoyee' => true,
        ]);
    }

    private function createUser(string $role, string $suffix = 'test'): User
    {
        return User::create([
            'username' => "{$role}.{$suffix}",
            'Nom' => 'Test',
            'Prenom' => ucfirst($role),
            'Adresse' => '1 rue du Garage',
            'NumeroTelephone' => '0102030405',
            'email' => "{$role}.{$suffix}@example.com",
            'password' => Hash::make('motdepasse-long-test'),
            'role' => $role,
        ]);
    }
}
