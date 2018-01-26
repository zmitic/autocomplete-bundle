
require('typeahead.js');
var $ = require('jquery');
var Bloodhound = require('bloodhound-js');

// when created dynamically
$(document).on('DOMNodeInserted', '.wjb-autocomplete', function (e) {
    var target = $(e.target);
    if(target.hasClass('wjb-autocomplete')) {
        init(target);
    }
});

// initial page load
$('.wjb-autocomplete').each(function () {
    init($(this));
});

function init(element) {
    var valueField = element.find('[data-value]');
    var idField = element.find('[data-id]');

    var remoteUrl = element.attr('data-remote-url');
    var suggestionsAsString = element.attr('data-suggestions');
    var suggestions = JSON.parse(suggestionsAsString);
    var debounce = element.attr('data-debounce');

    var bloodhoundSuggestions = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        sufficient: 3,
        local: suggestions,
        remote: {
            url: remoteUrl,
            wildcard: '_QUERY_',
            rateLimitWait: debounce,
            filter: function (response) {
                return $.grep(response, function (object) {
                    var isObjectInSuggestions = false;
                    $.each(suggestions, function (index, suggestion) {
                        if (suggestion.id === object.id) {
                            isObjectInSuggestions = true;
                        }
                    });

                    return !isObjectInSuggestions;
                });
            }
        }
    });

    valueField.typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'suggestions',
            displayKey: 'value',
            source: bloodhoundSuggestions
        })
        .bind('typeahead:select', function (event, datum) {
            event.preventDefault();
            idField.val(datum.id);
        })
        .bind('paste keyup submit', function (event) {
            event.preventDefault();
            if (event.keyCode === 13) {
                return;
            }
            idField.val('');
        });
}

