<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        function add_to_cart(url, btn) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: btn.parent().siblings().find('.cart-quantity').val()
                }
            }).done(function(response) {
                $('.cartCount').html(response.count);
                $('.cartTotal').html(response.total);
                if(response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }).fail(function(response) {
                toastr.error(response);
            })
        }

        //HANDLE CART
        $(document).on('click', '.btn-add-cart', function() {
            let url  = $(this).data('url'),
                delivery_url  = $(this).data('delivery-url'),
                btn = $(this);

            $.ajax({
                type: 'POST',
                url: delivery_url,
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: btn.parent().siblings().find('.cart-quantity').val()
                }
            }).done(function(response) {

                if(!response.status)
                    if(confirm(response.message))
                        add_to_cart(url, btn);
                else
                    add_to_cart(url, btn);

            }).fail(function(response) {
                toastr.error(response);
            })
        });
    });
</script>
