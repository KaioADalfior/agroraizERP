<x-layouts.app title="Banco de Dados">
    <x-page-header title="Banco de Dados" subtitle="Teores de nutrientes adequados por cultura, para alta produtividade" />

    <x-card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-raiz-200 bg-raiz-50 text-xs font-semibold uppercase tracking-wide text-raiz-600">
                    <tr>
                        <th class="px-6 py-3">Cultura</th>
                        <th class="px-6 py-3">P resina</th>
                        <th class="px-6 py-3">P mehlich</th>
                        <th class="px-6 py-3">K</th>
                        <th class="px-6 py-3">Ca</th>
                        <th class="px-6 py-3">Mg</th>
                        <th class="px-6 py-3">S</th>
                        <th class="px-6 py-3">B</th>
                        <th class="px-6 py-3">Zn</th>
                        <th class="px-6 py-3">Cu</th>
                        <th class="px-6 py-3">Fe</th>
                        <th class="px-6 py-3">Mn</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-raiz-100">
                    @foreach ($culturas as $cultura)
                        <tr class="hover:bg-raiz-50/60">
                            <td class="px-6 py-4 font-medium text-raiz-900">{{ $cultura->nome }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->p_resina ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->p_mehlich ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->k ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->ca ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->mg ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->s ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->b ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->zn ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->cu ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->fe ?? '—' }}</td>
                            <td class="px-6 py-4 text-raiz-700">{{ $cultura->mn ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-raiz-200 px-6 py-4 text-xs text-raiz-500">
            P resina/mehlich, S, B, Zn, Cu, Fe e Mn em mg/dm³. K, Ca e Mg em cmolc/dm³.
            Fonte: Prezotti et al. (2007).
        </div>
    </x-card>
</x-layouts.app>
