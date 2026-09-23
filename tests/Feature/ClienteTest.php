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

    public function test_busca_filtra_por_cidade(): void
    {
        Cliente::factory()->create(['nome' => 'Cliente Um', 'cidade' => 'Curaçá']);
        Cliente::factory()->create(['nome' => 'Cliente Dois', 'cidade' => 'Juazeiro']);

        $this->actingAs(User::factory()->create())
            ->get('/clientes?busca=Curaçá')
            ->assertSee('Cliente Um')
            ->assertDontSee('Cliente Dois');
    }

    public function test_cria_cliente(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', [
                'nome' => 'João da Silva',
                'cep' => '48260-000',
                'endereco' => 'Rua das Flores',
                'numero' => '123',
                'bairro' => 'Centro',
                'cidade' => 'Curaçá',
                'estado' => 'BA',
                'data' => '2026-01-15',
                'observacao' => 'Cliente novo',
            ])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', [
            'nome' => 'João da Silva',
            'cep' => '48260-000',
            'cidade' => 'Curaçá',
            'estado' => 'BA',
        ]);
    }

    public function test_cep_e_normalizado_ao_salvar(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', [
                'nome' => 'Maria Souza',
                'cep' => '48260000',
                'endereco' => 'Rua das Flores',
                'bairro' => 'Centro',
                'cidade' => 'Curaçá',
                'estado' => 'BA',
            ])
            ->assertRedirect('/clientes');

        $this->assertDatabaseHas('clientes', ['nome' => 'Maria Souza', 'cep' => '48260-000']);
    }

    public function test_nome_e_obrigatorio(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', ['nome' => ''])
            ->assertSessionHasErrors('nome');
    }

    public function test_estado_deve_ser_uma_sigla_valida(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/clientes', [
                'nome' => 'Cliente Teste',
                'cep' => '48260-000',
                'endereco' => 'Rua das Flores',
                'bairro' => 'Centro',
                'cidade' => 'Curaçá',
                'estado' => 'XX',
            ])
            ->assertSessionHasErrors('estado');
    }

    public function test_atualiza_cliente(): void
    {
        $cliente = Cliente::factory()->create(['nome' => 'Nome Antigo']);

        $this->actingAs(User::factory()->create())
            ->put("/clientes/{$cliente->id}", [
                'nome' => 'Nome Novo',
                'cep' => $cliente->cep,
                'endereco' => $cliente->endereco,
                'bairro' => $cliente->bairro,
                'cidade' => $cliente->cidade,
                'estado' => $cliente->estado,
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
