function handleResponse(res, form) {
    (res.error)
        ? Swal.fire('Error!', res.result ?? res.error, 'error')
        : (Swal.fire(res.title, res.result, 'success'), setTimeout(() => {
            res.reload ? window.location.reload() : (form && !res.no_reset ? form.reset() : null),
            res.redirect ? (window.location.href = res.redirect) : (form && !res.no_reset ? form.reset() : null)
        }, 2000));

}

jQuery(document).on('change', 'input', function (e) {
    setTimeout(() => {
        jQuery(e.target).data('element') ? submitForm(jQuery(e.target).data('form'), jQuery(e.target).data('element'),) : null;
    }, 100);
});

jQuery(document).on('change', 'input.search', function (e) {
    setTimeout(() => {
        link = '?' + jQuery(this).attr('name')+'='+jQuery(this).val();
        jQuery(this).val() ? loadPage(link, '') : null;
    }, 100);
});

jQuery(document).on('submit', '.ajax-form', function (e) {
    e.preventDefault();
    submitForm(e.target.id, jQuery(this).data('element'), jQuery(this).data('append'), jQuery(this).data('prepend'));
});

jQuery(document).on('click', '.ajax-load', function (e) {
    e.preventDefault();
    let path = jQuery(this).attr('href');
    let title = jQuery(this).attr('title');
    loadPage(path, title);

});

jQuery(document).on('click', '.ajax-link', function (e) {
    e.preventDefault();

    let data = jQuery(this).data('params');
    let path = jQuery(this).attr('href');
    let needConfirm = jQuery(this).data('confirm');
    let confirmText = jQuery(this).data('confirm-text');
    if (needConfirm) {
        Swal.fire({
            title: needConfirm,
            text: confirmText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
        }).then((result) => {
            if (result.isConfirmed) {
                submitLink(path, data);
            }
        });
    } else {
        submitLink(path, data);
    }
});

function submitForm(formId, elementId, append = null, prepend = null) {
    
    // Get the form and submit button elements
    const form = document.getElementById(formId);
    const element = jQuery('#'+elementId);

    if (!form)
        return null;
    
    jQuery('#page-loader').removeClass('hidden')

    // Get the form data as a FormData object
    const formData = new FormData(form);

    // Send the form data via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        jQuery('#page-loader').addClass('hidden')
        if (xhr.readyState === XMLHttpRequest.DONE ) {
            // Handle the successful response 
            try {

                let res = JSON.parse(xhr.responseText);
                return handleResponse(res, form)

            } catch (error) {
                if (xhr.responseText)
                {
                    if (append)
                        element.append(xhr.responseText)
                    else if (prepend)
                        element.prepend(xhr.responseText)
                    else 
                        element.html(xhr.responseText);

                }

            }
        }
    };
    xhr.send(formData);
}


// Submit Ajax request
function submitLink(path, data) {
    $.ajax({
        url: path,
        type: 'POST',
        dataType: 'JSON',
        contentType: 'application/json',
        data: JSON.stringify({ params: data }), // Your data to send
        processData: false,
        success: function (data) {
            // Update your UI with the new data
            handleResponse(data, null)
        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
}


// Submit Ajax request
function loadPage(path, title = '' ) {

    let mark = path.includes('?') ? '&' : '?'
    jQuery('#page-loader').removeClass('hidden')
    $.ajax({
        url: path + mark +'load=content',
        type: 'GET',
        processData: false,
        success: function (data) {
            // Update your UI with the new data
            jQuery('#app-content').html(data)
            window.history.pushState({"html":data,"pageTitle":title},"", path);
            document.title = title
            setTimeout(function() {
                // Alpine.initTree(document.getElementById('app-content'))
                reloadFuncs()
            }, 500)
            jQuery('#page-loader').addClass('hidden')
            window.scrollTo({top: 0, behavior: 'smooth'});

        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
}


// Submit Ajax request
function loadSection(path, elementId, loader = true ) {

    var element = jQuery('#'+elementId);
    if (loader) {
        element.html('')
        jQuery('#page-loader').removeClass('hidden')
    }
    
    $.ajax({
        url: path ,
        type: 'POST',
        processData: false,
        success: function (data) {
            // Update your UI with the new data
            if (!loader)
                element.prepend(data)
            else 
                element.html(data)

            setTimeout(function() {
                reloadFuncs()
            }, 500)
            jQuery('#page-loader').addClass('hidden')
        },
        error: function (xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
}


function pureFadeIn(e, o) {
    var i = document.getElementById(e);
    i.style.opacity = 0, i.style.display = o || "block",
        function e() {
            var o = parseFloat(i.style.opacity);
            (o += .02) > 1 || (i.style.opacity = o, requestAnimationFrame(e))
        }()
}


/**
 * Set cookies
 * @param {*} e 
 * @returns void
 */
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); // Convert days to milliseconds
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}


function getCookie(e) {
    for (var o = e + "=", i = document.cookie.split(";"), t = 0; t < i.length; t++) {
        for (var n = i[t]; " " == n.charAt(0);) {
            n = n.substring(1, n.length);
        }
        if (0 == n.indexOf(o))
            return n.substring(o.length, n.length)
    }
    return null
}

function appendHtml(el, str) {
    var div = document.createElement('div');
    div.innerHTML = str;
    while (div.children.length > 0) {
        el.appendChild(div.children[0]);
    }
}