<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Facades\Filament;

class NavigationSearch extends Component
{
    public string $search = '';
    
    public array $results = [];

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->results = [];
            return;
        }

        $this->results = $this->searchNavigation();
    }

    private function searchNavigation(): array
    {
        $results = [];
        $searchLower = mb_strtolower($this->search);
        
        // Pegar todos os recursos registrados
        $resources = Filament::getResources();
        
        foreach ($resources as $resource) {
            $label = $resource::getNavigationLabel();
            $group = $resource::getNavigationGroup();
            
            // Verificar se o label ou grupo contém o termo de busca
            if (
                str_contains(mb_strtolower($label), $searchLower) ||
                str_contains(mb_strtolower($group ?? ''), $searchLower)
            ) {
                $results[] = [
                    'label' => $label,
                    'group' => $group,
                    'icon' => $resource::getNavigationIcon(),
                    'url' => $resource::getUrl(),
                    'badge' => $resource::getNavigationBadge(),
                ];
            }
        }
        
        // Pegar todas as páginas registradas
        $pages = Filament::getPages();
        
        foreach ($pages as $page) {
            $label = $page::getNavigationLabel();
            $group = $page::getNavigationGroup();
            
            if (
                str_contains(mb_strtolower($label), $searchLower) ||
                str_contains(mb_strtolower($group ?? ''), $searchLower)
            ) {
                $results[] = [
                    'label' => $label,
                    'group' => $group,
                    'icon' => $page::getNavigationIcon(),
                    'url' => $page::getUrl(),
                    'badge' => $page::getNavigationBadge(),
                ];
            }
        }
        
        return $results;
    }

    public function render()
    {
        return view('livewire.navigation-search');
    }
}
