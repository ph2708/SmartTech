@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegação da Paginação" class="custom-pagination-nav" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; font-size: 0.9rem; color: #475569; flex-wrap: wrap; gap: 12px;">
        <div class="pagination-info" style="font-size: 0.85rem; color: #64748b;">
            <span>Mostrando <strong>{{ $paginator->firstItem() ?? 0 }}</strong> a <strong>{{ $paginator->lastItem() ?? 0 }}</strong> de <strong>{{ $paginator->total() }}</strong> resultados</span>
        </div>

        <div class="pagination-buttons" style="display: flex; align-items: center; gap: 6px;">
            {{-- Botão Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="page-link-disabled" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; color: #cbd5e1; cursor: not-allowed; border: 1px solid #e2e8f0; font-weight: bold;">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link-btn" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: white; color: #1e293b; text-decoration: none; border: 1px solid #cbd5e1; font-weight: bold; transition: all 0.2s;" title="Página Anterior">
                    ‹
                </a>
            @endif

            {{-- Links de Páginas Numeradas --}}
            @foreach ($elements as $element)
                {{-- Três pontos (...) --}}
                @if (is_string($element))
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; color: #94a3b8; font-weight: bold;">{{ $element }}</span>
                @endif

                {{-- Array de Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link-active" style="display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border-radius: 8px; background: #e63946; color: white; font-weight: 700; border: 1px solid #e63946; box-shadow: 0 2px 6px rgba(230,57,70,0.3);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="page-link-btn" style="display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 10px; border-radius: 8px; background: white; color: #1e293b; text-decoration: none; border: 1px solid #cbd5e1; font-weight: 600; transition: all 0.2s;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botão Próximo --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link-btn" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: white; color: #1e293b; text-decoration: none; border: 1px solid #cbd5e1; font-weight: bold; transition: all 0.2s;" title="Próxima Página">
                    ›
                </a>
            @else
                <span class="page-link-disabled" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; color: #cbd5e1; cursor: not-allowed; border: 1px solid #e2e8f0; font-weight: bold;">
                    ›
                </span>
            @endif
        </div>
    </nav>
@endif
