
let $ = require('jquery');
require('./typeahead.bundle');
let Bloodhound = require('./typeahead.bundle');

$(document).on('focusin', '[data-wjb-autocomplete-value]:not(.autocomplete-initialized)', function (event) {
    let valueField = $(this);
    valueField.addClass('autocomplete-initialized');

    let wrapper = valueField.closest('.wjb-autocomplete-simple');

    let idField = wrapper.find('[data-wjb-autocomplete-id]');

    let suggestionsAsString = wrapper.attr('data-suggestions');
    let suggestions = JSON.parse(suggestionsAsString);

    let bloodhoundSuggestions = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: function (match) {
            return match.id;
        },
        sufficient: 3,
        local: suggestions,
        remote: {
            url: wrapper.attr('data-remote-url'),
            wildcard: '_QUERY_',
            rateLimitWait: wrapper.attr('data-debounce')
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

    valueField.focus();

});


