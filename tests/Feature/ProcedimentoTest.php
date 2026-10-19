<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcedimentoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_pagina_de_edicao_do_cliente_renderiza_a_aba_procedimentos(): void
    {
        $cliente = Cliente::factory()->create(['nome' => 'Fazenda Raiz Forte']);
        $cliente->procedimentos()->create([
            'nome' => 'Custeio visível na ficha',
            'tipo_operacao' => 'Custeio',
        ]);

        $this->withoutVite();

        $this->actingAs(User::factory()->create())
            ->get("/clientes/{$cliente->id}/edit")
            ->assertOk()
            ->assertSee('Procedimentos')
            ->assertSee('Custeio visível na ficha');
    }

    public function test_visitante_nao_acessa_procedimentos(): void
    {
        $cliente = Cliente::factory()->create();

        $this->post("/clientes/{$cliente->id}/procedimentos", ['nome' => 'x'])
            ->assertRedirect('/login');
    }

    public function test_visitante_nao_acessa_lista_de_bancos(): void
    {
        $this->get('/procedimentos/bancos')->assertRedirect('/login');
    }

    public function test_cadastra_procedimento_e_responde_json_quando_pedido(): void
    {
        $cliente = Cliente::factory()->create();

        $resposta = $this->actingAs(User::factory()->create())
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Custeio Safra 25/26',
                'banco_nome' => 'BCO DO BRASIL S.A.',
                'banco_codigo' => 1,
                'tipo_operacao' => 'Custeio',
                'fase_proposta' => 'Em análise',
                'porcentagem_cobrada' => '2.50',
                'pagamento_recebido' => false,
            ]);

        $resposta->assertCreated()
            ->assertJsonPath('procedimento.nome', 'Custeio Safra 25/26')
            ->assertJsonPath('procedimento.cliente_id', $cliente->id);

        $this->assertDatabaseHas('procedimentos', [
            'cliente_id' => $cliente->id,
            'nome' => 'Custeio Safra 25/26',
            'banco_codigo' => 1,
            'tipo_operacao' => 'Custeio',
        ]);
    }

    public function test_cadastro_via_formulario_redireciona_para_aba_procedimentos(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Investimento Irrigação',
                'tipo_operacao' => 'Investimento',
            ])
            ->assertRedirect("/clientes/{$cliente->id}/edit?aba=procedimentos");
    }

    public function test_nome_e_obrigatorio(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => '',
                'tipo_operacao' => 'Custeio',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('nome');
    }

    public function test_tipo_de_operacao_e_obrigatorio_e_restrito_a_custeio_ou_investimento(): void
    {
        $cliente = Cliente::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", ['nome' => 'Sem tipo'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('tipo_operacao');

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Tipo inválido',
                'tipo_operacao' => 'Outro',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('tipo_operacao');

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Tipo válido',
                'tipo_operacao' => 'Investimento',
            ])
            ->assertCreated();
    }

    public function test_porcentagem_cobrada_nao_aceita_valores_fora_da_faixa_0_a_100(): void
    {
        $cliente = Cliente::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Negativo',
                'tipo_operacao' => 'Custeio',
                'porcentagem_cobrada' => '-1',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('porcentagem_cobrada');

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Maior que 100',
                'tipo_operacao' => 'Custeio',
                'porcentagem_cobrada' => '100.01',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('porcentagem_cobrada');

        $this->actingAs($user)
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Dentro da faixa',
                'tipo_operacao' => 'Custeio',
                'porcentagem_cobrada' => '100',
            ])
            ->assertCreated();
    }

    public function test_pagamento_recebido_e_falso_por_padrao_quando_omitido_e_nunca_e_inferido(): void
    {
        $cliente = Cliente::factory()->create();

        $this->actingAs(User::factory()->create())
            ->postJson("/clientes/{$cliente->id}/procedimentos", [
                'nome' => 'Sem campo de pagamento',
                'tipo_operacao' => 'Custeio',
                // 'pagamento_recebido' deliberadamente omitido
            ])
            ->assertCreated()
            ->assertJsonPath('procedimento.pagamento_recebido', false);

        $this->assertDatabaseHas('procedimentos', [
            'nome' => 'Sem campo de pagamento',
            'pagamento_recebido' => false,
        ]);
    }

    public function test_procedimento_fica_vinculado_ao_cliente_da_url_mesmo_se_outro_cliente_id_for_enviado(): void
    {
        $clienteCorreto = Cliente::factory()->create();
        $outroCliente = Cliente::factory()->create();

        $resposta = $this->actingAs(User::factory()->create())
            ->postJson("/clientes/{$clienteCorreto->id}/procedimentos", [
                'nome' => 'Procedimento isolado',
                'tipo_operacao' => 'Custeio',
                'cliente_id' => $outroCliente->id, // tentativa de forjar o cliente
            ])
            ->assertCreated();

        $this->assertSame($clienteCorreto->id, $resposta->json('procedimento.cliente_id'));
        $this->assertDatabaseHas('procedimentos', [
            'nome' => 'Procedimento isolado',
            'cliente_id' => $clienteCorreto->id,
        ]);
        $this->assertDatabaseMissing('procedimentos', [
            'nome' => 'Procedimento isolado',
            'cliente_id' => $outroCliente->id,
        ]);
    }

    public function test_atualiza_procedimento(): void
    {
        $cliente = Cliente::factory()->create();
        $procedimento = $cliente->procedimentos()->create([
            'nome' => 'Nome antigo',
            'tipo_operacao' => 'Custeio',
            'pagamento_recebido' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->putJson("/procedimentos/{$procedimento->id}", [
                'nome' => 'Nome novo',
                'tipo_operacao' => 'Investimento',
                'pagamento_recebido' => true,
            ])
            ->assertOk()
            ->assertJsonPath('procedimento.nome', 'Nome novo')
            ->assertJsonPath('procedimento.pagamento_recebido', true);

        $this->assertDatabaseHas('procedimentos', [
            'id' => $procedimento->id,
            'nome' => 'Nome novo',
            'tipo_operacao' => 'Investimento',
            'pagamento_recebido' => true,
        ]);
    }

    public function test_remove_procedimento_com_soft_delete(): void
    {
        $cliente = Cliente::factory()->create();
        $procedimento = $cliente->procedimentos()->create([
            'nome' => 'Procedimento a remover',
            'tipo_operacao' => 'Custeio',
        ]);

        $this->actingAs(User::factory()->create())
            ->deleteJson("/procedimentos/{$procedimento->id}")
            ->assertOk();

        $this->assertSoftDeleted('procedimentos', ['id' => $procedimento->id]);
    }

    public function test_bancos_quando_brasilapi_responde_normaliza_filtra_deduplica_e_ordena(): void
    {
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                ['ispb' => '00000000', 'name' => 'BCO DO BRASIL S.A.', 'code' => 1, 'fullName' => 'Banco do Brasil S.A.'],
                ['ispb' => '60701190', 'name' => 'ITAÚ UNIBANCO S.A.', 'code' => 341, 'fullName' => 'Itaú Unibanco S.A.'],
                // duplicado proposital (mesmo código) — precisa sobrar só um
                ['ispb' => '60701190', 'name' => 'ITAÚ UNIBANCO S.A.', 'code' => 341, 'fullName' => 'Itaú Unibanco S.A.'],
                // sem nome — precisa ser descartado
                ['ispb' => '00000001', 'name' => '', 'code' => 999],
                // sem código — precisa ser descartado
                ['ispb' => '00000002', 'name' => 'Banco sem código'],
            ], 200),
        ]);

        $resposta = $this->actingAs(User::factory()->create())
            ->getJson('/procedimentos/bancos')
            ->assertOk();

        $bancos = $resposta->json('bancos');

        $this->assertCount(2, $bancos);
        $this->assertNull($resposta->json('erro'));
        // Ordenado por nome: "Banco do Brasil S.A." vem antes de "Itaú Unibanco S.A."
        $this->assertSame('Banco do Brasil S.A.', $bancos[0]['nome']);
        $this->assertSame(1, $bancos[0]['codigo']);
        $this->assertSame('Itaú Unibanco S.A.', $bancos[1]['nome']);
        $this->assertSame(341, $bancos[1]['codigo']);
    }

    public function test_bancos_usa_cache_e_nao_chama_a_brasilapi_de_novo_na_segunda_requisicao(): void
    {
        Http::fake([
            'brasilapi.com.br/*' => Http::response([
                ['name' => 'BCO DO BRASIL S.A.', 'code' => 1, 'fullName' => 'Banco do Brasil S.A.'],
            ], 200),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/procedimentos/bancos')->assertOk();
        $this->actingAs($user)->getJson('/procedimentos/bancos')->assertOk();

        Http::assertSentCount(1);
    }

    public function test_bancos_quando_brasilapi_falha_e_nao_ha_cache_retorna_lista_vazia_com_erro(): void
    {
        Http::fake(['brasilapi.com.br/*' => Http::response('', 500)]);

        $resposta = $this->actingAs(User::factory()->create())
            ->getJson('/procedimentos/bancos')
            ->assertOk();

        $this->assertSame([], $resposta->json('bancos'));
        $this->assertTrue($resposta->json('erro'));
    }

    public function test_bancos_quando_brasilapi_falha_depois_do_cache_principal_vencer_usa_lista_de_reserva(): void
    {
        $user = User::factory()->create();

        // Uma única chamada Http::fake(), com uma sequência de respostas para a
        // mesma URL: a primeira chamada tem sucesso, a segunda falha. Chamar
        // Http::fake() de novo no meio do teste NÃO substitui a resposta
        // configurada antes (ela acumula e a primeira regra continua valendo) —
        // por isso a sequência precisa ser declarada de uma vez só aqui.
        Http::fake([
            'brasilapi.com.br/*' => Http::sequence()
                ->push([
                    ['name' => 'BCO DO BRASIL S.A.', 'code' => 1, 'fullName' => 'Banco do Brasil S.A.'],
                ], 200)
                ->push('', 500),
        ]);

        $this->actingAs($user)->getJson('/procedimentos/bancos')->assertOk();

        // Simula o cache principal (24h) vencendo, mas a cópia "de reserva"
        // (Cache::forever) continua guardada.
        Cache::forget('bancos_brasileiros');

        $resposta = $this->actingAs($user)
            ->getJson('/procedimentos/bancos')
            ->assertOk();

        $bancos = $resposta->json('bancos');
        $this->assertCount(1, $bancos);
        $this->assertSame('Banco do Brasil S.A.', $bancos[0]['nome']);
        $this->assertTrue($resposta->json('erro'));
    }
}
