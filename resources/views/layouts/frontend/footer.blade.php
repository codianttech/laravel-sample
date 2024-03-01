<div class="nk-footer">
    <div class="container-fluid">
        <div class="nk-footer-wrap justify-content-center">
            <div class="nk-footer-copyright">© {{date('Y')}} All Rights Reserved.</div>
        </div>
    </div>
</div>
@if(session()->has('success'))
<script nonce="{{ csp_nonce('script') }}">
    $(document).ready(function () {
        successToaster("{!! session('success') !!}");
    });
</script>
@endif
@if(session()->has('error'))
<script nonce="{{ csp_nonce('script') }}">
    $(document).ready(function () {
        errorToaster("{!! session('error') !!}");
    });
</script>
@endif
