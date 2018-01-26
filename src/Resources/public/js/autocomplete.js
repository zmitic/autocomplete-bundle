
var Bloodhound = require('bloodhound-js');
require('typeahead.js');

$('.wjb-autocomplete').each(function () {
    var field = $(this).find('[data-value]');
    var idField = $(this).find('[data-id]');
    var remoteUrl = $(this).attr('data-remote-url');
    var suggestionsAsString = $(this).attr('data-suggestions');
    var suggestions = JSON.parse(suggestionsAsString);
    console.log(suggestions);

    var bloodhoundSuggestions = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        sufficient: 3,

        identify: function (obj) {
            return  obj.id + '';
        },
        local: suggestions,
        // prefetch: '/prefetch',
        remote: {
            url: remoteUrl,
            wildcard: '_QUERY_',
            rateLimitWait: 100
        }
    });

    field.typeahead(
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
});



