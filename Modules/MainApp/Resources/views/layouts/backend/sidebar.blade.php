<aside class="sidebar" id="sidebar">

    <x-sidebar-header />

    <div class="sidebar-menu srollbar">
        <div class="sidebar-menu-section">


            <!-- parent menu list start  -->
            <ul class="sidebar-dropdown-menu">
                <li class="sidebar-menu-item {{ set_menu(['dashboard']) }}">
                    <a href="{{ route('mainapp.dashboard') }}" class="parent-item-content">
                        <i class="las la-desktop"></i>
                        <span class="on-half-expanded">{{ ___('mainapp_dashboard.Dashboard') }}</span>
                    </a>
                </li>

                {{-- System Admin Only Features (school_id = NULL) --}}
                @if(isSuperAdmin())
                    <li class="sidebar-menu-item {{ set_menu(['school*']) }}">
                        <a href="{{ route('school.index') }}" class="parent-item-content">
                            <i class="las la-users"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_schools.Schools') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['subscription.index']) }}">
                        <a href="{{ route('subscription.index') }}" class="parent-item-content">
                            <i class="las la-globe"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_subscriptions.Subscriptions') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['subscription-payments*']) }}">
                        <a href="{{ route('subscription-payments.index') }}" class="parent-item-content">
                            <i class="las la-money-check-alt"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_subscriptions.Subscription Payments') }}</span>
                        </a>
                    </li>
                    {{-- Hidden: Features menu item
                    <li class="sidebar-menu-item {{ set_menu(['feature*']) }}">
                        <a href="{{ route('feature.index') }}" class="parent-item-content">
                            <i class="las la-braille"></i>
                            <span class="on-half-expanded">{{ ___('common.Features') }}</span>
                        </a>
                    </li>
                    --}}
                    <li class="sidebar-menu-item {{ set_menu(['package*']) }}">
                        <a href="{{ route('package.index') }}" class="parent-item-content">
                            <i class="las la-bolt"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_package.Packages') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['feature-groups*']) }}">
                        <a href="{{ route('feature-groups.index') }}" class="parent-item-content">
                            <i class="las la-layer-group"></i>
                            <span class="on-half-expanded">{{ ___('common.Feature Groups') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['permission-features*']) }}">
                        <a href="{{ route('permission-features.index') }}" class="parent-item-content">
                            <i class="las la-shield-alt"></i>
                            <span class="on-half-expanded">{{ ___('common.Permission Features') }}</span>
                        </a>
                    </li>
                @endif
                @if(isSuperAdmin())
                    {{-- Reports Submenu --}}
                    <li class="sidebar-menu-item has-dropdown {{ set_menu(['reports*']) }}">
                        <a href="javascript:void(0)" class="parent-item-content has-arrow">
                            <i class="las la-chart-bar"></i>
                            <span class="on-half-expanded">{{ ___('common.Reports') }}</span>
                        </a>
                        <ul class="child-menu-list">
                            <li class="sidebar-menu-item {{ set_menu(['reports.payment-collection']) }}">
                                <a href="{{ route('reports.payment-collection') }}">
                                    <i class="las la-file-invoice-dollar"></i>
                                    <span>{{ ___('common.Payment Collection') }}</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ set_menu(['reports.school-growth']) }}">
                                <a href="{{ route('reports.school-growth') }}">
                                    <i class="las la-chart-line"></i>
                                    <span>{{ ___('common.School Growth') }}</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ set_menu(['reports.outstanding-payments']) }}">
                                <a href="{{ route('reports.outstanding-payments') }}">
                                    <i class="las la-exclamation-triangle"></i>
                                    <span>{{ ___('common.Outstanding Payments') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    {{-- Hidden: Payment report, Testimonials, FAQ, Contacts, Subscribes, Sections
                    <li class="sidebar-menu-item {{ set_menu(['payment-report*']) }}">
                        <a href="{{ route('payment.report.index') }}" class="parent-item-content">
                            <i class="las la-bolt"></i>
                            <span class="on-half-expanded">{{ ___('common.Payment report') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['testimonial*']) }}">
                        <a href="{{ route('testimonial.index') }}" class="parent-item-content">
                            <i class="las la-quote-left"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_testimonial.testimonials') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['faq*']) }}">
                        <a href="{{ route('faq.index') }}" class="parent-item-content">
                            <i class="las la-question"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_faq.FAQ') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['contact*']) }}">
                        <a href="{{ route('contacts') }}" class="parent-item-content">
                            <i class="las la-address-card"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_contact.Contacts') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['subscribe*']) }}">
                        <a href="{{ route('subscribes') }}" class="parent-item-content">
                            <i class="las la-bell"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_subscriptions.Subscribes') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['sections*']) }}">
                        <a href="{{ route('sections.index') }}" class="parent-item-content">
                            <i class="las la-list"></i>
                            <span class="on-half-expanded">{{ ___('common.Sections') }}</span>
                        </a>
                    </li>
                    --}}
                    <li class="sidebar-menu-item {{ set_menu(['languages*']) }}">
                        <a href="{{ route('languages.index') }}" class="parent-item-content">
                            <i class="las la-language"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_language.language') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ set_menu(['general-settings*']) }}">
                        <a href="{{ route('mainapp.settings.general-settings') }}" class="parent-item-content">
                            <i class="las la-cog"></i>
                            <span class="on-half-expanded">{{ ___('mainapp_settings.General settings') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
            <!-- parent menu list end  -->


        </div>


    </div>
</aside>
