<script>
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    toastr.error("{{ $error }}", "Error!");
    @endforeach
    @endif
    @if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}", "Success!");
    @endif
    @if(Session::has('error'))
    toastr.error("{{ Session::get('error') }}", "Error!");
    @endif
    @if(Session::has('info'))
    toastr.info("{{ Session::get('info') }}", "Info!");
    @endif
    @if(Session::has('warning'))
    toastr.warning("{{ Session::get('warning') }}", "Warning!");
    @endif
</script>
