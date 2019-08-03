// Read initial sitemap and build table with its contents
function readSitemapAndBuildTable()
{
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", "sitemap.xml", false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var lines = rawFile.responseText.split("\n");
                var tbody = document.getElementById("table-body");
                var rx = /^.*<loc>(.*)<\/loc>$/;
                lines.forEach(function (line, ind, arr) {
                    if (line.includes("<loc>")) {
                        extractedUrl = rx.exec(line)[1];

                        var tr = document.createElement("tr");
                        var tdLink = document.createElement("td");
                        var tdButton = document.createElement("td");

                        tdLink.innerHTML = "<a href=\"" + extractedUrl + "\">" + extractedUrl + "</a>";
                        tdButton.className = "text-center";
                        tdButton.innerHTML = "<button type=\"button\" class=\"btn btn-primary\">Delete</button>";

                        tr.appendChild(tdLink);
                        tr.appendChild(tdButton);
                        tbody.appendChild(tr);
                    }
                })
            } else {
                alert("Please, place \"sitemap.xml\" in the same directory as this page");
            }
        } else {
            alert("Please, place \"sitemap.xml\" in the same directory as this page");
        }
    };
    rawFile.send(null);
}

// Show "Save as" window
function downloadAs(data, filename, type) {
    var file = new Blob([data], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var a = document.createElement("a"),
            url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function () {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 0);
    }
}

$(document).ready(function() {
    readSitemapAndBuildTable();

    // Delete button
    $(".btn-primary").click(function() {
        $(this).parent().parent().hide("slow");
    });

    // Save changes button
    $(".btn-success").click(function() {
        // Collect links that not deleted
        var notDeletedLinks = [];
        var trs = document.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
        for (var i = 0; i < trs.length; i++) {
            if (trs[i].getAttribute("style") !== "display: none;") {
                notDeletedLinks.push(trs[i].getElementsByTagName("td")
                    .item(0)
                    .getElementsByTagName("a")
                    .item(0)
                    .getAttribute("href"));
            }
        }

        // Construct sitemap
        sitemap = [];
        sitemap.push("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
        sitemap.push("<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">");
        for (var i = 0; i < notDeletedLinks.length; i++)
            sitemap.push("\t<url>\n\t\t<loc>" + notDeletedLinks[i] + "</loc>\n\t</url>");
        sitemap.push("</urlset>");

        // Get user download new sitemap
        downloadAs(sitemap.join("\n"), "new-sitemap.xml", "application/xml");
    });
});
