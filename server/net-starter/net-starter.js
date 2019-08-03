// Check status until it's not "done"
function checkStatus(id) {
    $.get("/status?id=" + id.toString(), function (data) {
        crawlingStatusResponse = JSON.parse(data);
        status = crawlingStatusResponse['status'];

        if (status === "done") {
            crawlingResult = crawlingStatusResponse['crawlingResult'];
            // Redraw element card
            $("#card-body" + id.toString()).find(".card-body").html(crawlingResult['resultBody'])
        } else {
            setTimeout(function () {
                checkStatus(id);
            }, 5000);
        }
    });
}

// Define shorthand utility method
$.extend({
    el: function(el, props, content = "") {
        var $el = $(document.createElement(el));
        $el.attr(props);
        $el.html(content);
        return $el;
    }
});

// Submit task button
$("form").submit(function (event) {
    // Get data from the form
    var url = $(this).find("#url").val();
    var email = $(this).find("#email").val();

    $.post("/crawl",
        {
            url: url,
            email: email
        },
        function (data, status) {
            crawlingTaskResponse = JSON.parse(data);
            id = parseInt(crawlingTaskResponse['id'], 10);
            message = crawlingTaskResponse['message'];

            $("#crawlingModal").find(".modal-body").text(message);
            $("#crawlingModal").modal();

            if (id !== 0) { // 0 - Some error occured
                // Build task card
                var card = null;

                $(".container").append(
                    card = $.el('div', {'class': 'card mt-3'})
                );
                card.append(
                    $.el('div', {
                        'class': 'card-header text-white bg-info',
                        'data-toggle': 'collapse',
                        'data-target': '#card-body' + id.toString()
                    }, 'Results for ' + url)
                );
                card.append(
                    $.el('div', {
                        'class': 'collapse show',
                        'id': 'card-body' + id.toString()
                    }).append(
                        $.el('div', {'class': 'card-body'}, '<h5>Will appear soon...</h5>')
                    )
                );
                checkStatus(id);
            }
        });
});
