<nav class="mt-0">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @include('components.menu.nav-item-main', [
            'text' =>  __('Dashboard'),
            'href' => route('recruitment.dashboard.index'),
            'active' => 'recruitment/dashboard*',
            'icon' => 'fa-tachometer-alt',
        ])

        {{-- @include('components.menu.nav-item-main', [
            'text' =>  __('Dashboard'),
            'href' => route('recruitment.dashboard.index'),
            'active' => 'recruitment/dashboard*',
            'icon' => 'fa-tachometer-alt',
        ]) --}}

        @if(getAuthLevel()=="hrd")
            @component('components.menu.nav-treeview', [
                'text' => __('Payroll'),
                'actives' => [
                    'recruitment/payroll*',
                    'recruitment/thr*',
                    'recruitment/bonus*',
                    'recruitment/pesangon*',
                    'recruitment/pph*',
                    'recruitment/spt*',
                ],
                'icon' => 'fas fa-handshake',
            ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Proses Payroll'),
                    'href' => route('recruitment.payroll.index'),
                    'active' => 'recruitment/payroll*',
                    'icon' => 'fa-boxes',
                ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Upload THR'),
                    'href' => route('recruitment.thr.index'),
                    'active' => 'recruitment/thr*',
                    'icon' => 'fa-boxes',
                ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Upload Bonus'),
                    'href' => route('recruitment.bonus.index'),
                    'active' => 'recruitment/bonus*',
                    'icon' => 'fa-boxes',
                ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Upload Pesangon'),
                    'href' => route('recruitment.pesangon.index'),
                    'active' => 'recruitment/pesangon*',
                    'icon' => 'fa-boxes',
                ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Data PPH 21'),
                    'href' => route('recruitment.pph.index'),
                    'active' => 'recruitment/pph*',
                    'icon' => 'fa-boxes',
                ])
                @include('components.menu.nav-item', [
                    'text' =>  __('Proses SPT'),
                    'href' => route('recruitment.spt.index'),
                    'active' => 'recruitment/spt*',
                    'icon' => 'fa-boxes',
                ])
            @endcomponent
        @endif
    </ul>
</nav>
