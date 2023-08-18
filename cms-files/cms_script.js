var divs = document.querySelectorAll("div[id^='CMS-']");

var xhr = new XMLHttpRequest();
xhr.open("POST", "cms-files/cms_process.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

var formData = "";

for (var i = 0; i < divs.length; i++) {
    var divIdWithoutPrefix = divs[i].id.replace("CMS-", "");
    formData += "div_id[]=" + divIdWithoutPrefix + "&";
}

xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
        var response = xhr.responseText;
        var data = JSON.parse(response);

        for (var i = 0; i < divs.length; i++) {
            var divIdWithoutPrefix = divs[i].id.replace("CMS-", "");
            
            if (data[divIdWithoutPrefix]) {
                var divData = data[divIdWithoutPrefix];

                if (divData["header"]) {
                    var headerText = divData["header"];
                    var hTags = divs[i].querySelectorAll("h1, h2, h3, h4, h5, h6");
                    if (hTags.length > 0) {
                        hTags[0].textContent = headerText;
                    }
                }

                if (divData["paragraph"]) {
                    var paragraphText = divData["paragraph"];
                    var pTag = divs[i].querySelector("p");
                    if (pTag) {
                        pTag.textContent = paragraphText;
                    }
                }

                if (divData["link"]) {
                    var linkData = divData["link"];
                    var anchorTag = divs[i].querySelector("a");
                    if (anchorTag) {
                        anchorTag.href = linkData.url;
                        if (linkData.text) {
                            anchorTag.textContent = linkData.text;
                        }
                    }
                }

                if (divData["image"]) {
                    var imgTag = divs[i].querySelector("img");
                    if (imgTag) {
                        imgTag.src = divData["image"];
                        imgTag.alt = divData["header"];
                    }
                }
            }
        }
    }
};

xhr.send(formData);
