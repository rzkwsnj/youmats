<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ $staticImages->getFirstMediaUrlOrDefault(LOGO_PATH, 'size_height_25')['url'] }}" alt="{{env('APP_NAME')}}"
             class="brand-image">
        <span class="brand-text font-weight-light">&nbsp;</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ $vendor->getFirstMediaUrlOrDefault(VENDOR_LOGO, 'size_50_50')['url'] }}"
                     class="img-circle elevation-2"
                     alt="{{ $vendor->name }}">
            </div>
            <div class="info">
                <a class="d-block">{{ $vendor->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item item_Upgrade">
                    <a href="{{route('vendor.subscribe.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-star text-success"></i>
                        <p class="text-success">{{__('vendorAdmin.upgrade_your_membership')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.dashboard')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{__('vendorAdmin.dashboard')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('vendor.edit')}}" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>{{__('vendorAdmin.edit_info')}}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            {{__('vendorAdmin.products')}}
                            <i class="right fas fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('vendor.product.create')}}" class="nav-link">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>{{__('vendorAdmin.add_new_product')}}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('vendor.product.index')}}" class="nav-link">
                                <i class="fa fa-border-all nav-icon"></i>
                                <p>{{__('vendorAdmin.view_all')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-code-branch"></i>
                        <p>
                            {{__('vendorAdmin.branches')}}
                            <i class="right fas fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('vendor.branch.create')}}" class="nav-link">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>{{__('vendorAdmin.add_new_branch')}}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('vendor.branch.index')}}" class="nav-link">
                                <i class="fa fa-border-all nav-icon"></i>
                                <p>{{__('vendorAdmin.view_all')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>
                            {{__('vendorAdmin.shipping_groups')}}
                            <i class="right fas fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('vendor.shipping-group.create')}}" class="nav-link">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>{{__('vendorAdmin.add_new_shipping')}}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('vendor.shipping-group.index')}}" class="nav-link">
                                <i class="fa fa-border-all nav-icon"></i>
                                <p>{{__('vendorAdmin.view_all')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            {{__('vendorAdmin.orders')}}
                            <i class="right fas fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('vendor.order.index')}}" class="nav-link">
                                <i class="fa fa-border-all nav-icon"></i>
                                <p>{{__('vendorAdmin.view_all')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @if($vendor->current_subscribes && array_intersect($vendor->current_subscribes->pluck('membership_id')->toArray(), [env('COMPANY_MEMBERSHIP_ID'), env('BOTH_MEMBERSHIP_ID')]))
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>
                            {{__('vendorAdmin.quotes')}}
                            <i class="right fas fa-plus"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{route('vendor.quote.index')}}" class="nav-link">
                                <i class="fa fa-border-all nav-icon"></i>
                                <p>{{__('vendorAdmin.view_all')}}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{route('vendor.product.generate')}}" class="nav-link">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>{{__('vendorAdmin.request_generate_products')}}</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
