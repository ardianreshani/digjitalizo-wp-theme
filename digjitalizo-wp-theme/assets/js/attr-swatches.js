(function ($) {
    'use strict';

    function buildSwatches($form) {
        $form.find('.variations select').each(function () {
            var $select = $(this);
            var $td     = $select.closest('td.value');

            var $group = $('<div>', {
                'class'    : 'attr-swatches',
                'data-for' : $select.attr('name'),
            });

            $select.find('option[value!=""]').each(function () {
                var $opt = $(this);
                $group.append(
                    $('<button>', {
                        type          : 'button',
                        'class'       : 'attr-swatch',
                        'data-value'  : $opt.val(),
                        text          : $opt.text(),
                    })
                );
            });

            /* Keep select in DOM — WC needs it — just hide it visually */
            $select.addClass('attr-swatch-hidden');
            $td.prepend($group);

            $group.on('click', '.attr-swatch', function () {
                var $btn = $(this);
                if ($btn.hasClass('unavailable')) return;

                var val = $btn.data('value');

                /* Toggle: clicking the already-selected swatch resets the attribute */
                $select.val($select.val() === val ? '' : val);

                /* Let WC's variation JS handle everything from here */
                $select.trigger('change.wc-variation-form');

                syncGroup($group, $select);
            });
        });

        syncAllGroups($form);
    }

    function syncGroup($group, $select) {
        var current = $select.val();

        $group.find('.attr-swatch').each(function () {
            var $btn = $(this);
            var val  = $btn.data('value');
            var $opt = $select.find('option[value="' + val + '"]');

            $btn.toggleClass('selected', val === current);

            /*
             * WC marks an option as unavailable only after update_variation_values
             * runs (when at least one other attribute is chosen). Before that,
             * options have no .attached class, so we leave all buttons enabled.
             */
            var wcHasRun    = $opt.hasClass('attached');
            var unavailable = wcHasRun && ($opt.is(':disabled') || !$opt.hasClass('enabled'));
            $btn.toggleClass('unavailable', unavailable);
        });
    }

    function syncAllGroups($form) {
        $form.find('.variations select').each(function () {
            var $select = $(this);
            var $group  = $form.find('.attr-swatches[data-for="' + $select.attr('name') + '"]');
            if ($group.length) syncGroup($group, $select);
        });
    }

    $(function () {
        $('form.variations_form').each(function () {
            var $form = $(this);

            buildSwatches($form);

            /* Re-sync button states whenever WC updates option availability */
            $form.on(
                'woocommerce_variation_has_changed found_variation reset_data update_variation_values',
                function () { syncAllGroups($form); }
            );
        });
    });

}(jQuery));
