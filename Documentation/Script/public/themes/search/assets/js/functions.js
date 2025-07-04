'use strict';

jQuery(document).ready(function() {
    // Filters Collapse
    jQuery(document).on('click', '.filters-toggle', function() {
        if(jQuery(this).hasClass('filters-toggle-active')) {
            jQuery(this).removeClass('filters-toggle-active');
            jQuery('.filters-menu').hide();
        } else {
            jQuery(this).addClass('filters-toggle-active');
            jQuery('.filters-menu').show();
        }

        openPane(jQuery('.image-active'), {updateGallery: false, ignoreClose: true, scroll: false});
    });

    // Menu Collapse
    jQuery(document).on('click', '.menu-button', function() {
        var menuId = jQuery(this).data('db-id');

        // Remove all active button states
        jQuery('.menu-button:not(#db-'+menuId+')').removeClass('menu-button-collapsed icon-active');

        // Remove all active drop-down states
        jQuery('.menu').removeClass('menu-collapsed');

        // If the drop-down has already already opened
        if(jQuery('#db-'+menuId).hasClass('menu-button-collapsed')) {
            // Close the drop-down
            jQuery('#db-'+menuId).removeClass('menu-button-collapsed icon-active');
            return;
        }

        // Add the drop-down active class
        jQuery('#db-'+menuId).toggleClass('menu-button-collapsed icon-active');

        // Show the drop-down
        jQuery('#dd-'+menuId).toggleClass('menu-collapsed');
    });

    jQuery(document).on('click', '.filter-element', function() {
        // Remove all other filter dropdowns active states
        jQuery('.filter-element:not(#'+jQuery(this).attr('id')+')').removeClass('filter-element-active');

        // Add the dropdown active class
        jQuery(this).toggleClass('filter-element-active');
    });

    jQuery(document).on('click', '.notification-close-error, .notification-close-warning, .notification-close-success, .notification-close-info', function() {
        jQuery(this).parent().fadeOut("slow");
        return false;
    });

    // Focus the search box
    if(jQuery('#search-input').data('autofocus') == 1) {
        if(isTouchDevice() == false) {
            jQuery('#search-input').focus();
        }
    }

    // Clear search box
    jQuery(document).on('click', '#clear-button', function() {
        jQuery('#search-input').val('');
        jQuery('#search-input').focus();
    });

    // Search button submit
    jQuery(document).on('click', '#search-button', function() {
        var searchInput = jQuery('#search-input');

        // If the search input is not empty
        if(searchInput.val().length > 0) {
            closeSearch();
            loadPage(searchInput.data('search-url')+searchInput.data('search-path')+'?q='+encodeURIComponent(searchInput.val()));
        }
    });

    // Home page active search option
    jQuery(document).on('click', '.home-search-menu', function() {
        // Remove active classes if any
        jQuery('.home-search-menu').removeClass('home-search-menu-active');

        // Add active class to selected element
        jQuery(this).addClass('home-search-menu-active');

        // Update the search input with the new path
        jQuery('#search-input').data('search-path', jQuery(this).data('new-path'));
    });

    // Popup, Modals, Menus hide action
    jQuery(document).on('mouseup', function(e) {
        // All the divs that needs to be excepted when being clicked (including the divs themselves)
        var container = jQuery('.menu-button, .menu-content, .filter-element, .search-list, #search-input, #search-button');

        // If the element clicked isn't the container nor a descendant then hide elements
        if(!container.is(e.target) && container.has(e.target).length === 0) {
            // Close menu
            if(jQuery('.menu-button').hasClass('menu-button-collapsed')) {
                jQuery('.menu-button').click();
            }
            // Close Filters dropdowns
            if(jQuery('.filter-element').hasClass('filter-element-active')) {
                jQuery('.filter-element').removeClass('filter-element-active');
            }

            // Close search list
            closeSearch();
            jQuery('.content-home').removeClass('content-home-focus');
            jQuery('.header-home').removeClass('header-home-focus');
        }
    });

    document.addEventListener('scroll', function (event) {
        if(jQuery(event.target).hasClass('row-dragscroll')) {
            if(jQuery(event.target).scrollLeft() > 10) {
                jQuery(event.target).addClass('filters-fade-left');
            } else {
                jQuery(event.target).removeClass('filters-fade-left');
            }

            if(jQuery(event.target).scrollRight() > 10) {
                jQuery(event.target).addClass('filters-fade-right');
            } else {
                jQuery(event.target).removeClass('filters-fade-right');
            }
        }
    }, true);

    // Update the panel's position when browser is resized
    jQuery(window).on('resize', function() {
        setTimeout(function() {
            // If the div exists
            if(jQuery('.image-active').length) {
                openPane(jQuery('.image-active'), {updateGallery: false, ignoreClose: true, scroll: true});
            }
        }, 150);
    });

    jQuery(document).on('click', '.pane-next', function() {
        jQuery('.image-active').next('div').click();
    });

    jQuery(document).on('click', '.pane-prev', function() {
        jQuery('.image-active').prev('div').click();
    });

    jQuery(document).on('click', '.pane-close', function() {
        closePane();
    });

    jQuery(document).on('click', '.image-frame', function(e) {
        e.stopPropagation();
        e.preventDefault();
        openPane(this, {updateGallery: true, ignoreClose: false, scroll: true});
    });

    jQuery(document).on('click', '.read-more', function() {
        jQuery(this).hide();
        jQuery(this).prev().removeAttr('class');
    });

    jQuery(document).on('focus keyup', '#search-input', function(e) {

        var query = jQuery('#search-input').val();

        // Clear any previous search queues
        if(typeof(searchTimeout) !== "undefined") {
            clearTimeout(searchTimeout);
        }

        var key = (e.keyCode || e.which);

        // If the key is an identifiable one
        if(key > 0) {
            jQuery('.content-home').addClass('content-home-focus');
            jQuery('.header-home').addClass('header-home-focus');
        }

        // On enter do the search
        if(key == 13) {
            jQuery('#search-button').click();
            return false;
        }

        // If search suggestions are enabled
        if(jQuery(this).data('suggestions') > 0) {
            searchList(key);

            // If arrow keys are pressed
            if(key == 37 || key == 38 || key == 39 || key == 40 || key == 0) {
                if(query.length > 0) {
                    openSearch();
                }
                return;
            } else {
                closeSearch();
            }

            // Check if the new request is unique
            if(typeof(queryHistory) !== "undefined") {
                if(queryHistory == query) {
                    return false;
                }
            }

            // If the user typed in the search box
            if(query.length > 0) {
                window.searchTimeout = setTimeout(function() {
                    window.queryHistory = query;
                    search();
                    openSearch();
                }, 250);
            } else {
                closeSearch();
            }
        }
    });

    jQuery(document).on('keyup', function(e) {
        var key = (e.keyCode || e.which);

        // If the search input is not focused
        if(e.target.id != 'search-input') {
            if(key == 37) {
                jQuery('.image-active').prev('div').click();
            } else if(key == 39) {
                jQuery('.image-active').next('div').click();
            }
        }
    });

    reload();
});

/**
 * Detect if the browser device has touch capability
 */
function isTouchDevice() {
    return 'ontouchstart' in document.documentElement;
}

jQuery(document).on("click", "a:not([data-nd])", function() {
    var linkUrl = jQuery(this).attr('href');
    loadPage(linkUrl, 0, null);
    return false;
});

jQuery(window).bind('popstate', function() {
    var linkUrl = location.href;
    loadPage(linkUrl, 0, null);
});

/**
 * Send a GET or POST request dynamically
 *
 * @param   argUrl      Contains the page URL
 * @param   argParams   String or serialized params to be passed to the request
 * @param   argType     Decides the type of the request: 1 for POST; 0 for GET;
 * @param   options     Various misc options
 * @return  string
 */
function loadPage(argUrl, argType, argParams, options = {loadingBar: true}) {
    if(options.loadingBar) {
        loadingBar(1);
    }

    if(argType == 1) {
        argType = "POST";
    } else {
        argType = "GET";

        // Store the url to the last page accessed
        if(argUrl != window.location) {
            window.history.pushState({path: argUrl}, '', argUrl);
        }
    }

    // Request the page
    $.ajax({
        url: argUrl,
        type: argType,
        data: argParams,
        success: function(data) {
            // Parse the output
            try {
                var result = jQuery.parseJSON(data);

                $.each(result, function(item, value) {
                    if(item == "title") {
                        document.title = value;
                    } else if(['header', 'content', 'footer'].indexOf(item) > -1) {
                        jQuery('#'+item).replaceWith(value);
                    } else {
                        jQuery('#'+item).html(value);
                    }
                });
            } catch(e) {

            }

            // Scroll the document at the top of the page
            jQuery(document).scrollTop(0);

            // Reload functions
            reload();

            if(options.loadingBar) {
                loadingBar(0);
            }
        }
    })
}

/**
 * The loading bar animation
 *
 * @param   type    The type of animation, 1 for start, 0 for stop
 */
function loadingBar(type) {
    if(type) {
        jQuery("#loading-bar").show();
        jQuery("#loading-bar").width((50 + Math.random() * 30) + "%");
    } else {
        jQuery("#loading-bar").width("100%").delay(50).fadeOut(400, function() {
            jQuery(this).width("0");
        });
    }
}

/**
 * This function gets called every time a dynamic request is made
 */
function reload() {
    loadFlexImages();
    dragscroll.reset();
}

/**
 * Load the justified gallery
 */
function loadFlexImages() {
    jQuery("#images-results").flexImages({
        rowHeight: 175
    });
}

/**
 * Get search suggestions for a given query
 */
function search() {
    var searchInput = jQuery('#search-input');
    loadPage(searchInput.data('search-url')+searchInput.data('suggestions-path'), 1, {q: searchInput.val(), searchType: searchInput.data('search-path'), token_id: searchInput.data('token-id')}, {loadingBar: false});
}

/**
 * Opens the drop-down search suggestions
 */
function openSearch() {
    jQuery('.search-list').show();
}

/**
 * Closes the Search Results list
 */
function closeSearch() {
    jQuery('.search-list').hide();
}

/**
 * Select an item on up/down keys from the search results list
 *
 * @param   key     The key the user has pressed
 */
function searchList(key) {
    var listItems = jQuery('.search-list-item');
    var selected = listItems.filter('.list-item-selected');
    var current;

    if(key != 40 && key != 38) {
        return;
    }

    listItems.removeClass('list-item-selected');

    if(key == 40) {
        // If there's no selected item, or the selected item is the last element
        if(!selected.length || selected.is(':last-child')) {
            // Select the first element
            current = listItems.eq(0);
        } else {
            current = selected.next();
        }
    } else if(key == 38) {
        // If there's no selected item, or the selected item is the last element
        if(!selected.length || selected.is(':first-child')) {
            // Select the first element
            current = listItems.last();
        } else {
            current = selected.prev();
        }
    }

    // Add the selected class to the selected item
    current.addClass('list-item-selected');

    // Update the search input with the new value
    jQuery('#search-input').val(jQuery('.list-item-selected').text().trim());
}

/**
 * Open the Preview Pane
 *
 * @param   target  The target element
 * @param   options Various misc options
 */
function openPane(target, options = {updateGallery: false, ignoreClose: false, scroll: false}) {
    // Close the pane if you click on the same target twice
    if(options.ignoreClose == false) {
        if(jQuery(target).hasClass('image-active')) {
            closePane();
            return false;
        }
    }

    // Remove all active rows
    jQuery('.image-frame').removeClass('image-active-row');
    jQuery('.image-frame').removeClass('image-active');

    if(options.updateGallery == true) {
        // Hide the preview image
        jQuery('#pane-image').hide();
    } else {
        // If the high quality image is already loaded
        // Prevents showing a broken image when screen is resized while the image was still loading
        if(highQualityImage) {
            updatePaneImage();
        } else {
            updatePaneImage(1);
        }
    }

    // Add the active state to the image thumbnail
    jQuery(target).addClass('image-active');

    // Update the prev/next buttons
    var previewNext = jQuery('.image-active').next('div');
    var previewPrev = jQuery('.image-active').prev('div');
    if(!previewNext.length) {
        jQuery('.pane-next').addClass('button-disabled');
    } else {
        jQuery('.pane-next').removeClass('button-disabled');
    }
    if(!previewPrev.length) {
        jQuery('.pane-prev').addClass('button-disabled');
    } else {
        jQuery('.pane-prev').removeClass('button-disabled');
    }

    // Item's current position in doc
    var curTopPos = jQuery(target).offset().top;
    var curLeftPos = jQuery(target).offset().left;

    // Show the preview pane
    jQuery('.preview-pane').css({'top': (curTopPos+jQuery(target).outerHeight()+10)+'px'})
    jQuery('.preview-pane').show();

    if(options.updateGallery == true) {
        window.highQualityImage = false;
        // Load thumbnail image
        jQuery('#pane-thumb').attr('src', jQuery('.image-active img').attr('src')).show();
        // Load full-sized image
        jQuery('#pane-image').attr('src', jQuery(target).data('image-url'));
        // Description
        jQuery('#pane-image-size').html(jQuery(target).data('image-size'));
        // Description URLs
        jQuery('#pane-url-name').html(jQuery(target).data('image-name'));
        jQuery('#pane-url-name').attr('href', jQuery(target).data('image-host-url'));

        jQuery('#pane-url-url').html(jQuery(target).data('image-display-url'));
        jQuery('#pane-url-url').attr('href', jQuery(target).data('image-host-url'));
        // Description Buttons
        jQuery('#preview-button-host').attr('href', jQuery(target).data('image-host-url'));
        jQuery('#preview-button-image').attr('href', jQuery(target).data('image-source-url'));
        jQuery('#pane-url-image').attr('href', jQuery(target).data('image-url'));
    }

    // Hide the container (needed to recalculate the available viewport space
    jQuery('#pane-url-image').hide();

    var imgRatio = imageRatio(jQuery(target).data('w'), jQuery(target).data('h'), jQuery('.pane-image').width(), jQuery('.pane-image').height());

    // Update the image & url with the new sizes
    jQuery('#pane-image, #pane-thumb').attr('width', imgRatio.width).attr('height', imgRatio.height);
    jQuery('#pane-url-image').css({'width': imgRatio.width+'px', 'height': imgRatio.height+'px', 'display': 'block'});

    // Set the preview pane arrow
    jQuery('.preview-pane-arrow').css({'left': (curLeftPos+(jQuery(target).outerWidth(true)/2))-15+'px'});

    // Compare to the rest of the targets
    jQuery('.image-frame').each(function() {
        if(jQuery(this).offset().top == curTopPos) {
            jQuery(this).addClass('image-active-row');
        }
    });

    // Scroll the preview pane into view
    if(options.scroll == true) {
        jQuery('html, body').stop(true).animate({
            scrollTop: jQuery(target).offset().top-(jQuery('#header').height()+10)
        }, 200);
    }
}

/**
 * Update the Preview Pane Image
 *
 * @param   type    Use the thumbnail image
 */
function updatePaneImage(type) {
    window.highQualityImage = true;
    jQuery('#pane-thumb').hide();
    jQuery('#pane-image').show();

    if(type) {
        jQuery('#pane-image').hide();
        jQuery('#pane-thumb').show();
    }
}

/**
 * Close the Preview Pane
 */
function closePane() {
    jQuery('.preview-pane').hide();
    jQuery('.image-frame').removeClass('image-active-row');
    jQuery('.image-frame').removeClass('image-active');
}

/**
 * Calculate the aspect ratio based on available space.
 *
 * @param   imgWidth    Image width
 * @param   imgHeight   Image height
 * @param   maxWidth    Maximum available width
 * @param   maxHeight   maximum available height
 * @return  object
 */
function imageRatio(imgWidth, imgHeight, maxWidth, maxHeight) {
    var ratio = Math.min(maxWidth/imgWidth, maxHeight/imgHeight);

    return { width: imgWidth*ratio, height: imgHeight*ratio };
}

/**
 * Update the cookie law banner
 *
 * @param cookie_path
 */
function cookieLaw(cookie_path) {
    // Update the cookie
    setCookie('cookie_law', 1, new Date().getTime() + (10 * 365 * 24 * 60 * 60 * 1000), cookie_path);

    // Hide the banner
    jQuery('.cookie-law-container').hide();
}

/**
 * Get the value of a given cookie
 *
 * @param   name
 * @returns {*}
 */
function getCookie(name) {
    var name = name + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');

    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while(c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if(c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}

/**
 * Set a cookie
 *
 * @param   name
 * @param   value
 * @param   expire
 * @param   path
 */
function setCookie(name, value, expire, path) {
    document.cookie = name + "=" + value + ";expires=" + (new Date(expire).toUTCString()) + ";path=" + path;
}

/**
 * Get and format the user current time for Instant Answers
 *
 * @param   date_format The date format (with sprintf syntax)
 * @param   months      List of translated months
 * @param   type        The request type, 0 for time, 1 for date
 */
function iaUserDateTime(date_format, months, type) {
    if(typeof userTimeRunning !== "undefined") {
        clearTimeout(userTimeRunning);
    }

    var date = new Date();
    // var seconds = ('0' + date.getSeconds()).slice(-2);
    var minutes = ('0' + date.getMinutes()).slice(-2);
    var hours   = ('0' + date.getHours()).slice(-2);
    var days    = ('0' + date.getDate()).slice(-2);
    var month   = date.getMonth();
    var year    = date.getFullYear();
    date_format = date_format.replace('%1$s', year).replace('%2$s', months[month]).replace('%3$s', days);

    if(type) {
        jQuery('.web-ia-user-date .web-ia-content').html(date_format);
    } else {
        jQuery('.web-ia-user-time .web-ia-content').html(hours+':'+minutes);
        jQuery('.web-ia-user-time .web-ia-footer').html(date_format);
    }

    window.userTimeRunning = setTimeout(iaUserDateTime, 1000, date_format, months);
}

/**
 * Stopwatch function for Instant Answers
 */
function iaStopwatch() {
    var time = 0;
    var offset;
    var self = this;

    if(typeof stopwatchRunning !== "undefined") {
        clearInterval(stopwatchRunning);
    }

    function update() {
        if(self.running) {
            time += delta();
        }
        jQuery('.web-ia-stopwatch .web-ia-content').html(timeFormatter(time));
    }

    function delta() {
        var now = Date.now();
        var timePassed = now-offset;

        offset = now;

        return timePassed;
    }

    function timeFormatter(time) {
        time = new Date(time);

        var milliseconds = ('0' + time.getMilliseconds()).slice(-3).substr(0, 2);
        var seconds = ('0' + time.getSeconds()).slice(-2);
        var minutes = ('0' + time.getMinutes()).slice(-2);

        return minutes+':'+seconds+'.'+milliseconds;
    }

    self.start = function() {
        jQuery('#sw-start').hide();
        jQuery('#sw-stop').show();
        window.stopwatchRunning = setInterval(update.bind(self), 10);
        offset = Date.now();
        self.running = true;
    };

    self.stop = function() {
        jQuery('#sw-stop').hide();
        jQuery('#sw-start').show();
        clearInterval(stopwatchRunning);
        self.running = false;
    };

    self.reset = function() {
        time = 0;
        update();
    };

    self.running = false;
}

/**
 * Get the user's screen resolution
 */
function iaUserScreenResolution() {
    jQuery('.web-ia-user-screen-resolution-width').html(window.screen.width);
    jQuery('.web-ia-user-screen-resolution-height').html(window.screen.height);
}

$.fn.extend({
    scrollRight: function (val) {
        if(val === undefined) {
            return this[0].scrollWidth - (this[0].scrollLeft + this[0].clientWidth);
        }
        return this.scrollLeft(this[0].scrollWidth - this[0].clientWidth - val);
    }
});