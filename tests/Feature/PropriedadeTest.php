<?php

namespace Tests\Feature;

use App\Models\AnaliseSolo;
use App\Models\Cliente;
use App\Models\Propriedade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropriedadeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_visitante_nao_acessa_pagina_do_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->get("/clientes/{$cliente->id}")->assertRedirect('/login');
    }

    public function test_pagina_do_cliente_mostra_as_abas(): void
    {
        $cliente = Cliente::factory()->create(['nome' => 'Fazenda Raiz Forte']);

        $this->actingAs(User::factory()->create())
            ->get("/clientes/{$cliente->id}")
            ->assertOk()
            ->assertSee('Fazenda Raiz Forte')
            ->assertSee('Propriedades')
            ->assertSee('Análises de Solo');
    }

    public function test_cadastra_propriedade_para_o_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post("/clientes/{$cliente->id}/propriedades", [
                'nome' => 'Fazenda Boa Vista',
                'area_hectares' => '45.50',
                'localizacao' => 'Zona rural',
            ])
            ->assertRedirect("/clientes/{$cliente->id}");

        $this->assertDatabaseHas('propriedades', [
            'cliente_id' => $cliente->id,
            'nome' => 'Fazenda Boa Vista',
        ]);
    }

    public function test_nome_da_propriedade_e_obrigatorio(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post("/clientes/{$cliente->id}/propriedades", ['nome' => ''])
            ->assertSessionHasErrors('nome');
    }

    public function test_lista_propriedades_do_cliente_na_pagina(): void
    {
        $cliente = Cliente::factory()->create();
        Propriedade::factory()->for($cliente)->create(['nome' => 'Sítio das Palmeiras']);

        $this->actingAs(User::factory()->create())
            ->get("/clientes/{$cliente->id}")
            ->assertSee('Sítio das Palmeiras');
    }

    public function test_remove_propriedade(): void
    {
        $cliente = Cliente::factory()->create();
        $propriedade = Propriedade::factory()->for($cliente)->create();

        $this->actingAs(User::factory()->create())
            ->delete("/propriedades/{$propriedade->id}")
            ->assertRedirect("/clientes/{$cliente->id}");

        $this->assertSoftDeleted('propriedades', ['id' => $propriedade->id]);
    }

    public function test_lista_analises_de_solo_do_cliente_com_a_propriedade(): void
    {
        $cliente = Cliente::factory()->create();
        $propriedade = Propriedade::factory()->for($cliente)->create(['nome' => 'Área 01']);
        AnaliseSolo::factory()->for($propriedade)->create();

        $this->actingAs(User::factory()->create())
            ->get("/clientes/{$cliente->id}")
            ->assertSee('Área 01');
    }
}
