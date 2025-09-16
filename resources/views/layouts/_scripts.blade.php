<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
    var hostUrl = "{{ asset('assets/') }}";
</script>

{{-- <script src="{{ asset('assets/plugins/custom/leaflet/leaflet.bundle.js') }}" ></script> --}}
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>
<script src="{{ asset('assets/js/custom/mobile-nav.js') }}"></script>
<script src="{{ asset('leaflet/leaflet.js') }}"></script>
@stack('scripts')