<!DOCTYPE html>
<html lang="id">
	<!--begin::Head-->
	@include('layouts._head')
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
		<!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<a href="index.html" class="d-block d-lg-none mx-auto py-20">
					<img alt="Logo" src="assets/media/logos/default.svg" class="theme-light-show h-25px" />
					<img alt="Logo" src="assets/media/logos/default-dark.svg" class="theme-dark-show h-25px" />
				</a>
				<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
					<!--begin::Wrapper-->
					<div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
						<!--begin::Header-->
						<div class="d-flex flex-stack py-2">
							<!--begin::Back link-->
							<div class="me-2"></div>
							<!--end::Back link-->
						</div>
						<!--end::Header-->
						<div class="py-20">
							<form action="{{ route('login.post') }}" method="POST">
									@csrf

									@if(session()->has('loginError'))
										<div class="alert alert-danger mb-4 text-center">
											{{ session('loginError') }}
										</div>
									@endif

									<div class="card-body">
										<div class="text-start mb-4">
											<h1 class="text-gray-900 mb-2">Sign In</h1>
											<div class="text-gray-500 fw-semibold fs-6">Welcome back!</div>
										</div>

										<div class="mb-4">
											<input type="email" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="email"
												class="form-control @error('email') is-invalid @enderror" required />
											@error('email')
												<div class="invalid-feedback">
													{{ $message }}
												</div>
											@enderror
										</div>

										<div class="mb-4 position-relative">
											<input type="password" placeholder="Password" name="password" id="passwordInput" autocomplete="current-password"
												class="form-control @error('password') is-invalid @enderror" required />
											@error('password')
												<div class="invalid-feedback">
													{{ $message }}
												</div>
											@enderror
										</div>

										<div class="d-grid gap-2">
											<button type="submit" class="btn btn-primary">
												Sign In
											</button>
										</div>
									</div>
								</form>
							</div>
						<script>
							document.addEventListener('DOMContentLoaded', function () {
								const passwordInput = document.getElementById('passwordInput');
								const eyeIcon = document.getElementById('eyeIcon');
								const passwordAddon = document.getElementById('password-addon');

								if (passwordAddon) {
									passwordAddon.addEventListener('click', function () {
										const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
										passwordInput.setAttribute('type', type);

										// Ganti ikon mata
										if (type === 'text') {
											eyeIcon.classList.remove('ri-eye-fill');
											eyeIcon.classList.add('ri-eye-off-fill');
										} else {
											eyeIcon.classList.remove('ri-eye-off-fill');
											eyeIcon.classList.add('ri-eye-fill');
										}
									});
								}
							});
						</script>						<!--begin::Footer-->
						<div class="m-0">
							<!--begin::Toggle-->
							<button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, 0px">
								<img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3" src="assets/media/flags/united-states.svg" alt="" />
								<span data-kt-element="current-lang-name" class="me-2">English</span>
								<i class="ki-duotone ki-down fs-2 text-muted rotate-180 m-0"></i>
							</button>
							<!--end::Toggle-->
							<!--begin::Menu-->
							<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-4" data-kt-menu="true" id="kt_auth_lang_menu">
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="English">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/united-states.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">English</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="Spanish">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/spain.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">Spanish</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="German">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/germany.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">German</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="Japanese">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/japan.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">Japanese</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="French">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/france.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">French</span>
									</a>
								</div>
								<!--end::Menu item-->
							</div>
							<!--end::Menu-->
						</div>
						<!--end::Footer-->
					</div>
					<!--end::Wrapper-->
				</div>
				<div class="d-none d-lg-flex flex-lg-row-fluid w-50 bgi-size-cover bgi-position-y-center bgi-position-x-start bgi-no-repeat" style="background-image: url('{{ asset('assets/media/auth/bg11.png') }}')"></div>
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		 @include('layouts._scripts')

	</body>
</html>