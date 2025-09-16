<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <a href="#">
            <img alt="Logo" src="{{ asset('assets/media/logos/default.svg') }}"
                class="h-200px app-sidebar-logo-default theme-light-show" />
            <img alt="Logo" src="{{ asset('assets/media/logos/default-dark.svg') }}"
                class="h-200px app-sidebar-logo-default theme-dark-show" />
            <img alt="Logo" src="{{ asset('assets/media/logos/default-small.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-outline ki-black-left-line fs-3 rotate-180"></i>
        </div>
    </div>
    	<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        	<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
				<div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
					data-kt-scroll-activate="true" data-kt-scroll-height="auto"
					data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
					data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
					data-kt-scroll-save-state="true">
                	<div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
						<div class="menu-item">
							
							{{-- menu --}}
							<a class="menu-link" href="{{ route('users.index') }}">
								<span class="menu-icon">
									 <i class="ki-outline ki-users fs-2"></i>
	 							 </span>
	 							 <span class="menu-title">Users</span>
							</a>

							 <a class="menu-link" href="{{ url('/recognition') }}">
								<span class="menu-icon">
									 <i class="ki-outline ki-camera fs-2"></i>
								 </span>
								<span class="menu-title">Recognition</span>
							 </a>


							<a class="menu-link" href="#">
								<span class="menu-icon">
									<i class="ki-outline ki-exit-right fs-2"></i>
								</span>
								<span class="menu-title">
									<form action="/logout" method="post">
										@csrf
										<button type="submit" class="btn btn-link w-100 text-start menu-link px-3 py-2">
											<span class="menu-title">Logout</span>
										</button>
									</form>
								</span>
							</a>
						</div>
						{{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>