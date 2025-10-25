<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBanner extends CreateRecord
{
    protected static string $resource = BannerResource::class;

    protected static ?string $title = 'Criar Banner Moderno';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('template_hero')
                ->label('🎯 Template: Hero Principal')
                ->color('info')
                ->icon('heroicon-o-sparkles')
                ->action(function () {
                    $this->form->fill([
                        'title' => 'Banner Hero Principal',
                        'background_color' => '#1a202c',
                        'banner_height' => 600,
                        'content_alignment' => 'center',
                        'overlay_color' => '#000000',
                        'overlay_opacity' => 40,
                        'layers' => [
                            [
                                'type' => 'badge',
                                'data' => [
                                    'text' => 'NOVIDADE',
                                    'bg_color' => '#c41e3a',
                                    'text_color' => '#ffffff',
                                    'align' => 'center',
                                    'margin_bottom' => 20,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'Bem-vindo ao Nosso Site',
                                    'tag' => 'h1',
                                    'color' => '#ffffff',
                                    'font_size' => 48,
                                    'font_weight' => '800',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 20,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'Descubra nossas novidades e ofertas exclusivas',
                                    'tag' => 'p',
                                    'color' => '#e2e8f0',
                                    'font_size' => 18,
                                    'font_weight' => '400',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 30,
                                ],
                            ],
                            [
                                'type' => 'button',
                                'data' => [
                                    'text' => 'Saiba Mais',
                                    'url' => '#',
                                    'bg_color' => '#c41e3a',
                                    'text_color' => '#ffffff',
                                    'size' => 'lg',
                                    'align' => 'center',
                                    'border_radius' => 30,
                                    'target_blank' => false,
                                    'full_width' => false,
                                ],
                            ],
                        ],
                    ]);
                }),
            
            Actions\Action::make('template_news')
                ->label('📰 Template: Notícia Destaque')
                ->color('warning')
                ->icon('heroicon-o-newspaper')
                ->action(function () {
                    $this->form->fill([
                        'title' => 'Banner Notícia em Destaque',
                        'background_color' => '#c41e3a',
                        'banner_height' => 400,
                        'content_alignment' => 'center',
                        'overlay_opacity' => 0,
                        'layers' => [
                            [
                                'type' => 'badge',
                                'data' => [
                                    'text' => 'URGENTE',
                                    'bg_color' => '#ffffff',
                                    'text_color' => '#c41e3a',
                                    'align' => 'center',
                                    'margin_bottom' => 15,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'Última Notícia',
                                    'tag' => 'h2',
                                    'color' => '#ffffff',
                                    'font_size' => 36,
                                    'font_weight' => '700',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 15,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'Fique por dentro das últimas atualizações',
                                    'tag' => 'p',
                                    'color' => '#ffffff',
                                    'font_size' => 16,
                                    'font_weight' => '400',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 25,
                                ],
                            ],
                            [
                                'type' => 'button',
                                'data' => [
                                    'text' => 'Leia Mais',
                                    'url' => '#',
                                    'bg_color' => '#ffffff',
                                    'text_color' => '#c41e3a',
                                    'size' => 'md',
                                    'align' => 'center',
                                    'border_radius' => 5,
                                    'target_blank' => false,
                                    'full_width' => false,
                                ],
                            ],
                        ],
                    ]);
                }),
            
            Actions\Action::make('template_promo')
                ->label('🎁 Template: Promoção')
                ->color('success')
                ->icon('heroicon-o-gift')
                ->action(function () {
                    $this->form->fill([
                        'title' => 'Banner Promocional',
                        'background_color' => '#059669',
                        'banner_height' => 350,
                        'content_alignment' => 'center',
                        'overlay_opacity' => 0,
                        'layers' => [
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'SUPER OFERTA',
                                    'tag' => 'h3',
                                    'color' => '#ffffff',
                                    'font_size' => 20,
                                    'font_weight' => '600',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 10,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'data' => [
                                    'content' => 'Até 50% de Desconto',
                                    'tag' => 'h1',
                                    'color' => '#ffffff',
                                    'font_size' => 52,
                                    'font_weight' => '800',
                                    'text_align' => 'center',
                                    'margin_top' => 0,
                                    'margin_bottom' => 20,
                                ],
                            ],
                            [
                                'type' => 'button',
                                'data' => [
                                    'text' => 'Aproveitar Agora',
                                    'url' => '#',
                                    'bg_color' => '#ffffff',
                                    'text_color' => '#059669',
                                    'size' => 'lg',
                                    'align' => 'center',
                                    'border_radius' => 50,
                                    'target_blank' => false,
                                    'full_width' => false,
                                ],
                            ],
                        ],
                    ]);
                }),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Banner criado com sucesso!')
            ->body('O banner foi cadastrado e está disponível no sistema.')
            ->duration(5000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver listagem')
                    ->url($this->getResource()::getUrl('index'))
                    ->button(),
                \Filament\Notifications\Actions\Action::make('create_another')
                    ->label('Criar outro')
                    ->url($this->getResource()::getUrl('create'))
                    ->button(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Criar Banner'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Banner'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}
