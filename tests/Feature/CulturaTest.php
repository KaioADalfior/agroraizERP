<?php

namespace Tests\Feature;

use App\Models\Cultura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CulturaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_migration_semeia_as_48_culturas_da_planilha(): void
    {
        $this->assertSame(48, Cultura::count());
        $this->assertDatabaseHas('culturas', ['nome' => 'Soja', 'k' => 0.3069]);
        $this->assertDatabaseHas('culturas', ['nome' => 'Café Arábica']);
    }

    public function test_visitante_nao_acessa_banco_de_dados(): void
    {
        $this->get('/banco-de-dados')->assertRedirect('/login');
    }

    public function test_lista_culturas_no_banco_de_dados(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/banco-de-dados')
            ->assertOk()
            ->assertSee('Soja')
            ->assertSee('Milho');
    }
}
