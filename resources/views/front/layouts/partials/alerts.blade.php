<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        @if(Session::has('custom_error'))
        toastr.error("{{ Session::get('custom_error') }}", "Error!");
        @endif
        @if(Session::has('custom_success'))
        toastr.success("{{ Session::get('custom_success') }}", "Success!");
        @endif
        @if(Session::has('custom_info'))
        toastr.info("{{ Session::get('custom_info') }}", "Info!");
        @endif
        @if(Session::has('custom_warning'))
        toastr.warning("{{ Session::get('custom_warning') }}", "Warning!");
        @endif
    });
</script>


