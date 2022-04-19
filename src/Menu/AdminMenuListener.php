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
            ->addChild('supplier')
            ->setLabel('app.ui.supplier');

        // Add a submenu to it
        $newSubmenu
            ->addChild('supplier', [
                'route' => 'app_admin_supplier_index',
            ])
            ->setLabel('app.ui.supplier')
            ->setLabelAttribute('icon', 'certificate');
    }
}
