@php
    $dentroDoModal ??= false;
    $camposProcedimento = 'block w-full rounded-lg border bg-white px-3 py-1.5 text-sm text-raiz-900 shadow-xs transition placeholder:text-raiz-400 focus:outline-none focus:ring-2 focus:ring-raiz-600/20';
    $rotuloProcedimento = 'mb-1 block text-xs font-medium text-raiz-700';
@endphp
<div
    x-data="{
        procedimentos: @js($cliente->procedimentos),
        modalAberto: false,
        modoEdicao: false,
        procedimentoId: null,
        salvando: false,
        excluindo: false,
        confirmandoExclusao: false,
        erro: null,
        erros: {},
        bancos: [],
        bancosCarregando: false,
        bancosCarregados: false,
        bancosErro: false,
        bancosDesatualizados: false,
        buscaBanco: '',
        dropdownBancoAberto: false,
        form: { nome: '', banco_nome: '', banco_codigo: null, tipo_operacao: '', fase_proposta: '', porcentagem_cobrada: '', pagamento_recebido: false },
        vazia() {
            return { nome: '', banco_nome: '', banco_codigo: null, tipo_operacao: '', fase_proposta: '', porcentagem_cobrada: '', pagamento_recebido: false };
        },
        abrirNova() {
            this.modoEdicao = false;
            this.procedimentoId = null;
            this.form = this.vazia();
            this.buscaBanco = '';
            this.erros = {};
            this.erro = null;
            this.confirmandoExclusao = false;
            this.modalAberto = true;
            this.carregarBancos();
        },
        abrirEdicao(procedimento) {
            this.modoEdicao = true;
            this.procedimentoId = procedimento.id;
            this.form = {
                nome: procedimento.nome ?? '',
                banco_nome: procedimento.banco_nome ?? '',
                banco_codigo: procedimento.banco_codigo ?? null,
                tipo_operacao: procedimento.tipo_operacao ?? '',
                fase_proposta: procedimento.fase_proposta ?? '',
                porcentagem_cobrada: procedimento.porcentagem_cobrada ?? '',
                pagamento_recebido: !! procedimento.pagamento_recebido,
            };
            this.buscaBanco = this.form.banco_codigo
                ? (this.form.banco_codigo + ' - ' + this.form.banco_nome)
                : (this.form.banco_nome || '');
            this.erros = {};
            this.erro = null;
            this.confirmandoExclusao = false;
            this.modalAberto = true;
            this.carregarBancos();
        },
        async carregarBancos() {
            if (this.bancosCarregados || this.bancosCarregando) {
                return;
            }
            this.bancosCarregando = true;
            this.bancosErro = false;
            this.bancosDesatualizados = false;
            try {
                const resposta = await fetch('{{ route('procedimentos.bancos') }}', {
                    headers: { 'Accept': 'application/json' },
                });
                const dados = await resposta.json();
                this.bancos = Array.isArray(dados.bancos) ? dados.bancos : [];
                // 'erro' só desativa a busca quando não há NENHUMA lista para usar.
                // Se a BrasilAPI falhou agora mas existe uma lista de reserva (já
                // obtida com sucesso antes), a busca continua funcionando com ela —
                // só avisamos que pode estar um pouco desatualizada.
                this.bancosErro = this.bancos.length === 0;
                this.bancosDesatualizados = !! dados.erro && this.bancos.length > 0;
            } catch (erro) {
                this.bancos = [];
                this.bancosErro = true;
            } finally {
                this.bancosCarregando = false;
                this.bancosCarregados = true;
            }
        },
        bancosFiltrados() {
            const termo = this.buscaBanco.trim().toLowerCase();
            if (termo === '') {
                return this.bancos.slice(0, 30);
            }
            return this.bancos
                .filter(b => b.nome.toLowerCase().includes(termo) || String(b.codigo).includes(termo))
                .slice(0, 30);
        },
        selecionarBanco(banco) {
            this.form.banco_codigo = banco.codigo;
            this.form.banco_nome = banco.nome;
            this.buscaBanco = banco.codigo + ' - ' + banco.nome;
            this.dropdownBancoAberto = false;
        },
        aoDigitarBanco() {
            // Enquanto a busca de bancos estiver indisponível, o campo funciona como
            // texto livre (nunca trava o resto do formulário).
            if (this.bancosErro) {
                this.form.banco_nome = this.buscaBanco;
                this.form.banco_codigo = null;
            } else {
                this.dropdownBancoAberto = true;
                if (this.form.banco_codigo && this.buscaBanco !== (this.form.banco_codigo + ' - ' + this.form.banco_nome)) {
                    this.form.banco_codigo = null;
                    this.form.banco_nome = '';
                }
            }
        },
        async salvar() {
            this.salvando = true;
            this.erros = {};
            this.erro = null;

            const url = this.modoEdicao
                ? '{{ url('procedimentos') }}/' + this.procedimentoId
                : '{{ route('procedimentos.store', $cliente) }}';

            try {
                const resposta = await fetch(url, {
                    method: this.modoEdicao ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                if (resposta.status === 422) {
                    const dados = await resposta.json();
                    this.erros = dados.errors || {};
                    return;
                }

                if (! resposta.ok) {
                    this.erro = 'Não foi possível salvar. Tente novamente.';
                    return;
                }

                const dados = await resposta.json();

                if (this.modoEdicao) {
                    const indice = this.procedimentos.findIndex(p => p.id === this.procedimentoId);
                    if (indice !== -1) {
                        this.procedimentos[indice] = dados.procedimento;
                    }
                } else {
                    this.procedimentos.push(dados.procedimento);
                }

                this.modalAberto = false;
            } catch (erro) {
                this.erro = 'Falha de conexão. Verifique sua internet e tente novamente.';
            } finally {
                this.salvando = false;
            }
        },
        async excluir() {
            this.excluindo = true;
            this.erro = null;

            try {
                const resposta = await fetch('{{ url('procedimentos') }}/' + this.procedimentoId, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                });

                if (! resposta.ok) {
                    this.erro = 'Não foi possível excluir. Tente novamente.';
                    return;
                }

                this.procedimentos = this.procedimentos.filter(p => p.id !== this.procedimentoId);
                this.modalAberto = false;
            } catch (erro) {
                this.erro = 'Falha de conexão. Verifique sua internet e tente novamente.';
            } finally {
                this.excluindo = false;
                this.confirmandoExclusao = false;
            }
        },
    }"
>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-raiz-900">Procedimentos cadastrados</h2>
        <x-button type="button" x-on:click="abrirNova()" :compact="true">+ Novo procedimento</x-button>
    </div>

    <template x-if="procedimentos.length === 0">
        <x-card class="{{ $dentroDoModal ? 'p-6' : 'p-10' }} text-center">
            <p class="text-sm text-raiz-600">Nenhum procedimento cadastrado ainda para este cliente.</p>
        </x-card>
    </template>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" x-show="procedimentos.length > 0">
        <template x-for="procedimento in procedimentos" :key="procedimento.id">
            <button
                type="button"
                x-on:click="abrirEdicao(procedimento)"
                class="rounded-xl border border-raiz-200 bg-white p-3 text-left shadow-xs transition hover:border-raiz-300 hover:bg-raiz-50/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
            >
                <div class="flex items-start justify-between gap-2">
                    <h3 class="truncate text-sm font-semibold text-raiz-900" x-text="procedimento.nome"></h3>
                    <span
                        class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                        x-bind:class="procedimento.pagamento_recebido ? 'bg-raiz-100 text-raiz-700' : 'bg-raiz-50 text-raiz-500'"
                        x-text="procedimento.pagamento_recebido ? 'Recebido' : 'Pendente'"
                    ></span>
                </div>
                <p class="mt-0.5 truncate text-sm text-raiz-600" x-text="procedimento.banco_nome || 'Sem banco informado'"></p>
                <p class="mt-0.5 text-xs text-raiz-500" x-text="[procedimento.tipo_operacao, procedimento.fase_proposta].filter(Boolean).join(' · ')"></p>
                <p class="mt-0.5 text-xs text-raiz-500" x-show="procedimento.porcentagem_cobrada" x-text="procedimento.porcentagem_cobrada + '% cobrado'"></p>
            </button>
        </template>
    </div>

    {{-- Modal: cadastro e edição de procedimento (mesmo componente para os dois modos) --}}
    <div
        x-show="modalAberto"
        x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center bg-raiz-950/60 p-4"
        x-on:keydown.escape.window="modalAberto = false"
    >
        <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl" x-on:click.outside="modalAberto = false">
            <div class="flex items-center justify-between border-b border-raiz-200 px-5 py-3">
                <h2 class="text-sm font-semibold text-raiz-900" x-text="modoEdicao ? 'Editar procedimento' : 'Novo procedimento'"></h2>
                <button
                    type="button"
                    x-on:click="modalAberto = false"
                    class="rounded-lg p-1.5 text-raiz-500 transition hover:bg-raiz-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
                    aria-label="Fechar"
                >
                    <x-icon name="close" class="size-5" />
                </button>
            </div>

            <form x-on:submit.prevent="salvar()" class="overflow-y-auto px-5 py-4">
                <p class="mb-3 text-xs text-raiz-500">Cliente: <strong class="text-raiz-700">{{ $cliente->nome }}</strong></p>

                <div x-show="erro" x-cloak class="mb-3 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    <x-icon name="alert" class="size-4 shrink-0" />
                    <span x-text="erro"></span>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="{{ $rotuloProcedimento }}">Nome do procedimento</label>
                        <input type="text" x-model="form.nome" placeholder="Ex.: Custeio Safra 25/26" class="{{ $camposProcedimento }}" x-bind:class="erros.nome ? 'border-red-400' : 'border-raiz-300'">
                        <p class="mt-1 text-xs text-red-600" x-show="erros.nome" x-text="erros.nome ? erros.nome[0] : ''"></p>
                    </div>

                    {{-- Banco: busca com autocomplete quando a lista carrega; texto livre se a fonte externa falhar --}}
                    <div class="relative sm:col-span-2" x-on:click.outside="dropdownBancoAberto = false">
                        <label class="{{ $rotuloProcedimento }}">Banco</label>
                        <input
                            type="text"
                            x-model="buscaBanco"
                            x-on:focus="dropdownBancoAberto = ! bancosErro"
                            x-on:input="aoDigitarBanco()"
                            placeholder="Digite o nome ou código do banco"
                            class="{{ $camposProcedimento }}"
                            x-bind:class="erros.banco_nome || erros.banco_codigo ? 'border-red-400' : 'border-raiz-300'"
                        >
                        <p class="mt-1 text-xs text-raiz-500" x-show="bancosCarregando" x-cloak>Carregando lista de bancos…</p>
                        <p class="mt-1 text-xs text-red-600" x-show="bancosErro && ! bancosCarregando" x-cloak>Não foi possível carregar a lista de bancos agora. Digite o nome manualmente.</p>
                        <p class="mt-1 text-xs text-raiz-500" x-show="bancosDesatualizados && ! bancosCarregando" x-cloak>Não foi possível atualizar a lista agora; mostrando a última lista obtida com sucesso.</p>

                        <div
                            x-show="dropdownBancoAberto && ! bancosErro && ! bancosCarregando"
                            x-cloak
                            class="absolute z-10 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-raiz-200 bg-white shadow-lg"
                        >
                            <template x-for="banco in bancosFiltrados()" :key="banco.codigo">
                                <button
                                    type="button"
                                    x-on:click="selecionarBanco(banco)"
                                    class="block w-full truncate px-3 py-1.5 text-left text-sm text-raiz-800 hover:bg-raiz-50"
                                    x-text="banco.codigo + ' - ' + banco.nome"
                                ></button>
                            </template>
                            <template x-if="bancosFiltrados().length === 0">
                                <p class="px-3 py-2 text-xs text-raiz-500">Nenhum banco encontrado.</p>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="{{ $rotuloProcedimento }}">Tipo de operação</label>
                        <select x-model="form.tipo_operacao" class="{{ $camposProcedimento }}" x-bind:class="erros.tipo_operacao ? 'border-red-400' : 'border-raiz-300'">
                            <option value="">Selecione</option>
                            @foreach (\App\Models\Procedimento::TIPOS_OPERACAO as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-red-600" x-show="erros.tipo_operacao" x-text="erros.tipo_operacao ? erros.tipo_operacao[0] : ''"></p>
                    </div>

                    <div>
                        <label class="{{ $rotuloProcedimento }}">Fase da proposta</label>
                        <input
                            type="text"
                            x-model="form.fase_proposta"
                            list="fases-proposta-sugeridas"
                            placeholder="Ex.: Em elaboração"
                            class="{{ $camposProcedimento }}"
                            x-bind:class="erros.fase_proposta ? 'border-red-400' : 'border-raiz-300'"
                        >
                        <datalist id="fases-proposta-sugeridas">
                            <option value="Em elaboração"></option>
                            <option value="Enviada"></option>
                            <option value="Em análise"></option>
                            <option value="Aprovada"></option>
                            <option value="Reprovada"></option>
                            <option value="Contratada"></option>
                        </datalist>
                        <p class="mt-1 text-xs text-raiz-500">Sugestões aparecem ao digitar, mas qualquer texto é aceito.</p>
                    </div>

                    <div>
                        <label class="{{ $rotuloProcedimento }}">Porcentagem cobrada</label>
                        <div class="relative">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                x-model="form.porcentagem_cobrada"
                                placeholder="Ex.: 2.5"
                                class="{{ $camposProcedimento }} pr-8"
                                x-bind:class="erros.porcentagem_cobrada ? 'border-red-400' : 'border-raiz-300'"
                            >
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-raiz-500">%</span>
                        </div>
                        <p class="mt-1 text-xs text-red-600" x-show="erros.porcentagem_cobrada" x-text="erros.porcentagem_cobrada ? erros.porcentagem_cobrada[0] : ''"></p>
                    </div>

                    <div>
                        <label class="{{ $rotuloProcedimento }}">Pagamento recebido</label>
                        <div class="inline-flex overflow-hidden rounded-lg border border-raiz-300">
                            <button
                                type="button"
                                x-on:click="form.pagamento_recebido = false"
                                x-bind:class="! form.pagamento_recebido ? 'bg-raiz-700 text-white' : 'bg-white text-raiz-600 hover:bg-raiz-50'"
                                class="px-3 py-1.5 text-sm font-semibold transition"
                            >
                                Não
                            </button>
                            <button
                                type="button"
                                x-on:click="form.pagamento_recebido = true"
                                x-bind:class="form.pagamento_recebido ? 'bg-raiz-700 text-white' : 'bg-white text-raiz-600 hover:bg-raiz-50'"
                                class="border-l border-raiz-300 px-3 py-1.5 text-sm font-semibold transition"
                            >
                                Sim
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Confirmação de exclusão (só no modo edição) --}}
                <div x-show="confirmandoExclusao" x-cloak class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700">
                    <p>Excluir este procedimento? Essa ação não pode ser desfeita pela tela.</p>
                    <div class="mt-2 flex items-center gap-2">
                        <x-button type="button" variant="danger" :compact="true" x-on:click="excluir()" x-bind:disabled="excluindo">
                            <span x-show="! excluindo">Confirmar exclusão</span>
                            <span x-show="excluindo" x-cloak>Excluindo…</span>
                        </x-button>
                        <x-button type="button" variant="ghost" :compact="true" x-on:click="confirmandoExclusao = false">Cancelar</x-button>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between gap-2 border-t border-raiz-200 pt-3">
                    <button
                        type="button"
                        x-show="modoEdicao"
                        x-cloak
                        x-on:click="confirmandoExclusao = true"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                    >
                        Excluir
                    </button>
                    <div class="ml-auto flex items-center gap-2">
                        <x-button type="button" variant="ghost" :compact="true" x-on:click="modalAberto = false">Cancelar</x-button>
                        <x-button type="submit" :compact="true" x-bind:disabled="salvando">
                            <span x-show="! salvando">Salvar</span>
                            <span x-show="salvando" x-cloak>Salvando…</span>
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
