<?php

namespace App\Services;

class AdminMenuService
{
    public static function getMenuItems()
    {
        return [
            [
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard'
            ],
            [
                'title' => 'E-Commerce',
                'icon' => 'fas fa-shopping-bag',
                'children' => [
                    [
                        'title' => 'Products',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.products',
                        'active' => 'admin.products*'
                    ],
                    [
                        'title' => 'Categories',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.categories',
                        'active' => 'admin.categories*'
                    ],
                    [
                        'title' => 'Orders',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.orders',
                        'active' => 'admin.orders*'
                    ]
                ]
            ],
            [
                'title' => 'Services',
                'icon' => 'fas fa-star',
                'children' => [
                    [
                        'title' => 'Consultations',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.consultations',
                        'active' => 'admin.consultations*'
                    ],
                    [
                        'title' => 'Pooja Bookings',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.consultations',
                        'active' => 'admin.consultations*'
                    ]
                ]
            ],
            [
                'title' => 'Content Management',
                'icon' => 'fas fa-file-alt',
                'children' => [
                    [
                        'title' => 'Pages',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.cms.pages',
                        'active' => 'admin.cms.pages*'
                    ],
                    [
                        'title' => 'Dynamic Pages',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.dynamic-pages.index',
                        'active' => 'admin.dynamic-pages*'
                    ],
                    [
                        'title' => 'Categories',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.cms.categories',
                        'active' => 'admin.cms.categories*'
                    ],
                    [
                        'title' => 'Page Types',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.cms.page-types',
                        'active' => 'admin.cms.page-types*'
                    ],
                    [
                        'title' => 'Comments',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.cms.comments',
                        'active' => 'admin.cms.comments*'
                    ]
                ]
            ],
            [
                'title' => 'Lists Management',
                'icon' => 'fas fa-list',
                'children' => [
                    [
                        'title' => 'Product Lists',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.lists.products',
                        'active' => 'admin.lists.products*'
                    ],
                    [
                        'title' => 'Page Lists',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.lists.pages',
                        'active' => 'admin.lists.pages*'
                    ],
                    [  
                        'title' => 'Templates',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.lists.templates',
                        'active' => 'admin.lists.templates*'
                    ]
                ]
            ],
            [
                'title' => 'User Management',
                'icon' => 'fas fa-users',
                'children' => [
                    [
                        'title' => 'Customers',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.users',
                        'active' => 'admin.users*'
                    ],
                    [
                        'title' => 'Admin Users',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.admins',
                        'active' => 'admin.admins*'
                    ]
                ]
            ],
            [
                'title' => 'Communication',
                'icon' => 'fas fa-envelope',
                'children' => [
                    [
                        'title' => 'Contact Submissions',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.contact.submissions',
                        'active' => 'admin.contact.submissions*'
                    ],
                    [
                        'title' => 'Contact Settings',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.contact.settings',
                        'active' => 'admin.contact.settings*'
                    ],
                    [
                        'title' => 'Notifications',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.notifications',
                        'active' => 'admin.notifications*'
                    ]
                ]
            ],
            [
                'title' => 'System Configuration',
                'icon' => 'fas fa-cog',
                'children' => [
                    [
                        'title' => 'General Settings',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.settings',
                        'active' => 'admin.settings'
                    ],
                    [
                        'title' => 'Footer Settings',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.footer.settings',
                        'active' => 'admin.footer*'
                    ],
                    [
                        'title' => 'Languages',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.languages',
                        'active' => 'admin.languages*'
                    ],
                    [
                        'title' => 'Currencies',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.currencies',
                        'active' => 'admin.currencies*'
                    ],
                    [
                        'title' => 'Payment Gateways',
                        'icon' => 'far fa-circle',
                        'route' => 'admin.payment-gateways',
                        'active' => 'admin.payment-gateways*'
                    ]
                ]
            ]
        ];
    }
}
