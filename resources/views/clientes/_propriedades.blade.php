@php
    $dentroDoModal ??= false;
    $camposPropriedade = 'block w-full rounded-lg border bg-white px-3 py-1.5 text-sm text-raiz-900 shadow-xs transition placeholder:text-raiz-400 focus:outline-none focus:ring-2 focus:ring-raiz-600/20';
    $rotuloPropriedade = 'mb-1 block text-xs font-medium text-raiz-700';
@endphp
<div
    x-data="{
        propriedades: @js($cliente->propriedades),
        modalAberto: false,
        modoEdicao: false,
        propriedadeId: null,
        salvando: false,
        excluindo: false,
        confirmandoExclusao: false,
        erro: null,
        erros: {},
        buscandoCep: false,
        erroCep: false,
        form: { nome: '', cep: '', endereco: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '', area_hectares: '', localizacao: '', observacao: '' },
        vazia() {
            return { nome: '', cep: '', endereco: '', numero: '', complemento: '', bairro: '', cidade: '', estado: '', area_hectares: '', localizacao: '', observacao: '' };
        },
        abrirNova() {
            this.modoEdicao = false;
            this.propriedadeId = null;
            this.form = this.vazia();
            this.erros = {};
            this.erro = null;
            this.erroCep = false;
            this.confirmandoExclusao = false;
            this.modalAberto = true;
        },
        abrirEdicao(propriedade) {
            this.modoEdicao = true;
            this.propriedadeId = propriedade.id;
            this.form = {
                nome: propriedade.nome ?? '',
                cep: propriedade.cep ?? '',
                endereco: propriedade.endereco ?? '',
                numero: propriedade.numero ?? '',
                complemento: propriedade.complemento ?? '',
                bairro: propriedade.bairro ?? '',
                cidade: propriedade.cidade ?? '',
                estado: propriedade.estado ?? '',
                area_hectares: propriedade.area_hectares ?? '',
                localizacao: propriedade.localizacao ?? '',
                observacao: propriedade.observacao ?? '',
            };
            this.erros = {};
            this.erro = null;
            this.erroCep = false;
            this.confirmandoExclusao = false;
            this.modalAberto = true;
        },
        formatarCep() {
            let numeros = this.form.cep.replace(/\D/g, '').slice(0, 8);
            if (numeros.length > 5) {
                numeros = numeros.slice(0, 5) + '-' + numeros.slice(5);
            }
            this.form.cep = numeros;
        },
        async buscarCep() {
            const cepLimpo = this.form.cep.replace(/\D/g, '');
            if (cepLimpo.length !== 8) {
                return;
            }
            this.buscandoCep = true;
            this.erroCep = false;
            try {
                const resposta = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                const dados = await resposta.json();
                if (dados.erro) {
                    this.erroCep = true;
                } else {
                    this.form.endereco = dados.logradouro || this.form.endereco;
                    this.form.bairro = dados.bairro || this.form.bairro;
                    this.form.cidade = dados.localidade || this.form.cidade;
                    this.form.estado = dados.uf || this.form.estado;
                }
            } catch (erro) {
                this.erroCep = true;
            } finally {
                this.buscandoCep = false;
            }
        },
        async salvar() {
            this.salvando = true;
            this.erros = {};
            this.erro = null;

            const url = this.modoEdicao
                ? '{{ url('propriedades') }}/' + this.propriedadeId
                : '{{ route('propriedades.store', $cliente) }}';

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
                    const indice = this.propriedades.findIndex(p => p.id === this.propriedadeId);
                    if (indice !== -1) {
                        this.propriedades[indice] = dados.propriedade;
                    }
                } else {
                    this.propriedades.push(dados.propriedade);
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
                const resposta = await fetch('{{ url('propriedades') }}/' + this.propriedadeId, {
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

                this.propriedades = this.propriedades.filter(p => p.id !== this.propriedadeId);
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
        <h2 class="text-sm font-semibold text-raiz-900">Propriedades cadastradas</h2>
        <x-button type="button" x-on:click="abrirNova()" :compact="true">+ Nova propriedade</x-button>
    </div>

    <template x-if="propriedades.length === 0">
        <x-card class="{{ $dentroDoModal ? 'p-6' : 'p-10' }} text-center">
            <p class="text-sm text-raiz-600">Nenhuma propriedade cadastrada ainda para este cliente.</p>
        </x-card>
    </template>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" x-show="propriedades.length > 0">
        <template x-for="propriedade in propriedades" :key="propriedade.id">
            <button
                type="button"
                x-on:click="abrirEdicao(propriedade)"
                class="rounded-xl border border-raiz-200 bg-white p-3 text-left shadow-xs transition hover:border-raiz-300 hover:bg-raiz-50/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-raiz-500"
            >
                <h3 class="truncate text-sm font-semibold text-raiz-900" x-text="propriedade.nome"></h3>
                <p class="mt-0.5 truncate text-sm text-raiz-600" x-text="[propriedade.cidade, propriedade.estado].filter(Boolean).join('/') || propriedade.localizacao || 'Sem endereço cadastrado'"></p>
                <p class="mt-0.5 text-xs text-raiz-500" x-show="propriedade.area_hectares" x-text="propriedade.area_hectares + ' ha'"></p>
            </button>
        </template>
    </div>

    {{-- Modal: cadastro e edição de propriedade (mesmo componente para os dois modos) --}}
    <div
        x-show="modalAberto"
        x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center bg-raiz-950/60 p-4"
        x-on:keydown.escape.window="modalAberto = false"
    >
        <div class="flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl" x-on:click.outside="modalAberto = false">
            <div class="flex items-center justify-between border-b border-raiz-200 px-5 py-3">
                <h2 class="text-sm font-semibold text-raiz-900" x-text="modoEdicao ? 'Editar propriedade' : 'Nova propriedade'"></h2>
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
                        <label class="{{ $rotuloPropriedade }}">Nome da propriedade</label>
                        <input type="text" x-model="form.nome" placeholder="Ex.: Fazenda Boa Vista" class="{{ $camposPropriedade }}" x-bind:class="erros.nome ? 'border-red-400' : 'border-raiz-300'">
                        <p class="mt-1 text-xs text-red-600" x-show="erros.nome" x-text="erros.nome ? erros.nome[0] : ''"></p>
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">CEP</label>
                        <input
                            type="text"
                            x-model="form.cep"
                            x-on:input="formatarCep()"
                            x-on:blur="buscarCep()"
                            placeholder="00000-000"
                            inputmode="numeric"
                            maxlength="9"
                            class="{{ $camposPropriedade }}"
                            x-bind:class="erros.cep ? 'border-red-400' : 'border-raiz-300'"
                        >
                        <p class="mt-1 text-xs text-raiz-500" x-show="buscandoCep" x-cloak>Buscando endereço…</p>
                        <p class="mt-1 text-xs text-red-600" x-show="erroCep" x-cloak>CEP não encontrado. Preencha manualmente.</p>
                        <p class="mt-1 text-xs text-red-600" x-show="erros.cep" x-text="erros.cep ? erros.cep[0] : ''"></p>
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Número</label>
                        <input type="text" x-model="form.numero" placeholder="Ex.: 123" class="{{ $camposPropriedade }}" x-bind:class="erros.numero ? 'border-red-400' : 'border-raiz-300'">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="{{ $rotuloPropriedade }}">Rua</label>
                        <input type="text" x-model="form.endereco" placeholder="Rua, avenida, estrada..." class="{{ $camposPropriedade }}" x-bind:class="erros.endereco ? 'border-red-400' : 'border-raiz-300'">
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Complemento</label>
                        <input type="text" x-model="form.complemento" placeholder="Apto, galpão, referência..." class="{{ $camposPropriedade }}">
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Bairro</label>
                        <input type="text" x-model="form.bairro" class="{{ $camposPropriedade }}" x-bind:class="erros.bairro ? 'border-red-400' : 'border-raiz-300'">
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Cidade</label>
                        <input type="text" x-model="form.cidade" class="{{ $camposPropriedade }}" x-bind:class="erros.cidade ? 'border-red-400' : 'border-raiz-300'">
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Estado</label>
                        <select x-model="form.estado" class="{{ $camposPropriedade }}" x-bind:class="erros.estado ? 'border-red-400' : 'border-raiz-300'">
                            <option value="">Selecione</option>
                            @foreach (\App\Models\Cliente::ESTADOS as $sigla => $nomeEstado)
                                <option value="{{ $sigla }}">{{ $sigla }} - {{ $nomeEstado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Área (ha)</label>
                        <input type="number" step="0.01" min="0" x-model="form.area_hectares" placeholder="Ex.: 45.5" class="{{ $camposPropriedade }}" x-bind:class="erros.area_hectares ? 'border-red-400' : 'border-raiz-300'">
                    </div>

                    <div>
                        <label class="{{ $rotuloPropriedade }}">Localização (notas)</label>
                        <input type="text" x-model="form.localizacao" placeholder="Ex.: km 12, portão verde" class="{{ $camposPropriedade }}">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="{{ $rotuloPropriedade }}">Observação</label>
                        <textarea x-model="form.observacao" rows="2" class="{{ $camposPropriedade }}"></textarea>
                    </div>
                </div>

                {{-- Confirmação de exclusão (só no modo edição) --}}
                <div x-show="confirmandoExclusao" x-cloak class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-sm text-red-700">
                    <p>Excluir esta propriedade? Essa ação não pode ser desfeita pela tela.</p>
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
