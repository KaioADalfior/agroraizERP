<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_visitante_nao_acessa_clientes(): void
    {
        $this->get('/clientes')->assertRedirect('/login');
    }

    public function test_lista_clientes_cadastrados(): void
    {
        Cliente::factory()->create(['nome' => 'Edson Ferreira']);

        $this->actingAs(User::factory()->create())
            ->get('/clientes')
            ->assertOk()
            ->assertSee('Edson Ferreira');
    }

    public function test_busca_filtra_por_nome(): void
    {
        Cliente::factory()->create(['nome' => 'Fazenda Boa Vista']);
        Cliente::factory()->create(['nome' => 'Sitio Recanto']);

        $this->actingAs(User::factory()->create())
            ->get('/clientes?busca=Boa+Vista')
            ->assertSee('Fazenda Boa Vista')
            ->assertDontSee('Sitio Recanto');
    }

    public function test_cria_cliente(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', [
                'nome' => 'João da Silva',
                'cidade_estado' => 'Curaçá-BA',
                'data' => '2026-01-15',
                'observacao' => 'Cliente novo',
            ])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', ['nome' => 'João da Silva']);
    }

    public function test_nome_e_obrigatorio(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', ['nome' => ''])
            ->assertSessionHasErrors('nome');
    }

    public function test_atualiza_cliente(): void
    {
        $cliente = Cliente::factory()->create(['nome' => 'Nome Antigo']);

        $this->actingAs(User::factory()->create())
            ->put("/clientes/{$cliente->id}", [
                'nome' => 'Nome Novo',
                'cidade_estado' => $cliente->cidade_estado,
            ])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'nome' => 'Nome Novo']);
    }

    public function test_remove_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete("/clientes/{$cliente->id}")
            ->assertRedirect('/clientes');

        $this->assertSoftDeleted('clientes', ['id' => $cliente->id]);
    }
}
