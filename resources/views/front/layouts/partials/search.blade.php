<script>

    document.addEventListener('DOMContentLoaded', function() {
        let xhr,
            searchDiv = $("#searchDiv");

        function AdjustProperties(){
            if(screen.availWidth > 760) {
                let PopupSuggestion = document.getElementById("SearchInnerDiv"),
                    SearchBarDiv   = document.getElementById("SearchBar"),
                    elementStyle = window.getComputedStyle(SearchBarDiv);

                PopupSuggestion.setAttribute("style", "left:61px;width:" + elementStyle.width + ";position: relative;");
            }
        }

        function doTheSuggestion(url) {
            if(xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            xhr = $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    searchDiv.removeClass('d-none');
                    searchDiv.html(response);
                    AdjustProperties();
                }
            });
        }
        function doTheMagic(url) {
            if(xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            window.location = url;
        }

        $(document).on('ready', function () {
            $("#searchProductInput, #searchProductInputMobile").keyup( function(e) {
                let WebSearchSuggest    = $("#searchProductInput").val();
                let MobileSearchSuggest = $("#searchProductInputMobile").val();

                let searchText = (WebSearchSuggest.length > 0) ? WebSearchSuggest : MobileSearchSuggest;
                if (searchText.length > 3) {
                    doTheSuggestion("{{ route('products.suggest') }}?filter[name]=" + searchText);

                    $("#searchProductBtn, #searchProductBtnMobile").click( function(e) {
                        let url = '{{ route("products.search", ":searched_word") }}';
                        url = url.replace(':searched_word', searchText.replace(/\s+/g, '-').toLowerCase());
                        doTheMagic(url);
                    });
                }
            });

            $("#searchProductInput, #searchProductInputMobile").click( function() {
                $("#u-header__section").css({"position":"static"});
                $("#header").css({"position":"static"});
                $("#SearchBar").css({"z-index":"99999"});
                $("#SearchBarMoblie").css({"z-index":"99999"});
                $("#SearchFace").css({"width":"100%","height":"1000vh","background":"black","position":"absolute","z-index":"1005","opacity":"0.5"});
            });

            $(document).on('click', '#SearchFace', function() {
                $("#u-header__section").css({"position":"relative"});
                $("#header").css({"position":"relative"});
                $("#SearchBar").css({"z-index":"999"});
                $("#SearchBarMoblie").css({"z-index":"999"});
                $("#SearchFace").css({"width":"","height":"","background":"","position":"","z-index":"","opacity":""});
                searchDiv.addClass('d-none');
            });

            let input = document.getElementById("searchProductInput");
            input.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("searchProductBtn").click();
                }
            });

            let mobileInput = document.getElementById("searchProductInputMobile");
            mobileInput.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("searchProductBtnMobile").click();
                }
            });

        });

    });
</script>
