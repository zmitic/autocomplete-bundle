
var $ = require('jquery');
require('./typeahead.bundle');
var Bloodhound = require('./typeahead.bundle');

// when created dynamically
$(document).on('DOMNodeInserted', '.wjb-autocomplete-simple', function (e) {
    var target = $(e.target);
    if(target.hasClass('wjb-autocomplete-simple')) {
        init(target);
    }
});

// initial page load
$('.wjb-autocomplete-simple').each(function () {
    init($(this));
});

function init(element) {
    var valueField = element.find('[data-value]');
    var idField = element.find('[data-id]');

    var suggestionsAsString = element.attr('data-suggestions');
    var suggestions = JSON.parse(suggestionsAsString);

    var bloodhoundSuggestions = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: function (match) {
            return match.id;
        },
        sufficient: 3,
        local: suggestions,
        remote: {
            url: element.attr('data-remote-url'),
            wildcard: '_QUERY_',
            rateLimitWait: element.attr('data-debounce')
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

