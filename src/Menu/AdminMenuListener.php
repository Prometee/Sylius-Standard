<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        // Add a main sub menu item
        $newSubmenu = $menu
            ->addChild('agglo')
            ->setLabel('app.ui.agglo');

        // Add a submenu to it
        $newSubmenu
            ->addChild('agglo', [
                'route' => 'app_admin_billetique_card_index',
            ])
            ->setLabel('app.ui.billetique_card')
            ->setLabelAttribute('icon', 'card');

        $menu->reorderChildren(
            ['agglo', 'catalog', 'sales', 'customers', 'marketing', 'configuration']
        );
    }
}
