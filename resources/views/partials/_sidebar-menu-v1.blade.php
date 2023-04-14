<nav class="mt-0">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @include('components.menu.nav-item', [
            'text' =>  __('Dashboard'),
            'href' => route('home'),
            'active' => 'home*',
            'icon' => 'fa-tachometer-alt',
        ])

        @include('components.menu.nav-item', [
            'text' =>  __('Map Gudang'),
            'href' => route('home'),
            'active' => 'map*',
            'icon' => 'fa-warehouse',
        ])

        @component('components.menu.nav-treeview', [
            'text' => __('Permintaan'),
            'actives' => [
                'po/service-request*',
                'po/service-order*',
                'wh/goods-borrow*',
                'wh/goods-release*',
                'wh/approvals-goods-release*',
            ],
            'icon' => 'fa-edit',
        ])
            @include('components.menu.nav-item', [
                'text' =>  __('Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-boxes',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Jasa'),
                'href' => route('po.service.request.index'),
                'active' => 'po/service-request*',
                'icon' => 'fa-tools',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Pinjam Barang'),
                'href' => route('wh.goods.borrow.index'),
                'active' => 'wh/goods-borrow*',
                'icon' => 'fa-business-time',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Pengajuan Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-shopping-bag',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Pembelian Barang (PO)'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-luggage-cart',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Pengadaan Jasa (PO)'),
                'href' => route('po.service.order.index'),
                'active' => 'po/service-order*',
                'icon' => 'fa-shipping-fast',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Retur Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-exchange-alt',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Keluar Barang'),
                'href' => route('wh.goods.release.index'),
                'active' => 'wh/goods-release*',
                'icon' => 'fa-door-open',
            ])
             @include('components.menu.nav-item', [
                'text' =>  __('Persetujuan Keluar Barang'),
                'href' => route('wh.goods.release.approvals'),
                'active' => 'wh/approvals-goods-release*',
                'icon' => 'fa-door-open',
            ])
        @endcomponent

        @component('components.menu.nav-treeview', [
            'text' => __('Stok'),
            'actives' => [
                'permintaan*',
                'izin*',
            ],
            'icon' => 'fa-chart-area',
        ])
             @include('components.menu.nav-item', [
                'text' =>  __('Masuk Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-dolly-flatbed',
            ])
             @include('components.menu.nav-item', [
                'text' =>  __('Simpan Barang Ke Rak'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fab fa-buromobelexperte',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Stok Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-boxes',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Riwayat Barang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-history',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Stok Kurang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-chart-bar',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Stok Opname'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-tasks',
            ])
        @endcomponent

        @component('components.menu.nav-treeview', [
            'text' => __('Master Data'),
            'actives' => [
                'stock*',
                'izin*',
                'supplier*',
                'general/unit*',
            ],
            'icon' => 'fa-th-list',
        ])
            @include('components.menu.nav-item', [
                'text' =>  __('Supplier'),
                'href' => route('supplier.index'),
                'active' => 'supplier',
                'icon' => 'fa-store',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Barang'),
                'href' => route('stock.item.index'),
                'active' => 'stock/item',
                'icon' => 'fa-boxes',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Kategori'),
                'href' => route('stock.item.category.index'),
                'active' => 'stock/item/category',
                'icon' => 'fa-layer-group',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Satuan'),
                'href' => route('general.unit.index'),
                'active' => 'general/unit*',
                'icon' => 'fa-balance-scale',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Merek'),
                'href' => route('stock.item.brand.index'),
                'active' => 'stock/item/brand',
                'icon' => 'fa-tag',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Denah Gudang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fas fa-sitemap',
            ])
            @include('components.menu.nav-item', [
                'text' =>  __('Barang Rak Gudang'),
                'href' => route('home'),
                'active' => 'barang*',
                'icon' => 'fa-warehouse',
            ])

        @endcomponent
    </ul>
</nav>
