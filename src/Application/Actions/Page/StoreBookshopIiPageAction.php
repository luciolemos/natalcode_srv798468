<?php

declare(strict_types=1);

namespace App\Application\Actions\Page;

class StoreBookshopIiPageAction extends StoreBookshopPageAction
{
    protected function getTemplate(): string
    {
        return 'pages/store-bookshop-ii.twig';
    }

    protected function getFallbackBasePath(): string
    {
        return '/loja/livraria';
    }

    protected function getPageTitle(): string
    {
        return 'NatalCode Labs | Loja | NatalCode';
    }

    protected function getPageDescription(): string
    {
        return 'Vitrine principal da NatalCode Labs, '
            . 'com catalogo publico organizado em lista e ficha tecnica expandida.';
    }
}
