<?php
defined('ABSPATH') || exit;

// ── Helpers ───────────────────────────────────────────────────────────────────

function emsaks_get_filterable_attributes(): array {
    return (array) get_option('emsaks_attr_filterable', []);
}

function emsaks_is_attribute_filterable(string $attribute_name): bool {
    $map = emsaks_get_filterable_attributes();
    return !empty($map[$attribute_name]);
}

// ── Admin: add checkbox to "Add attribute" form ───────────────────────────────

add_action('woocommerce_after_add_attribute_fields', function () {
    ?>
    <div class="form-field">
        <label for="attribute_filterable">
            <input type="checkbox" name="attribute_filterable" id="attribute_filterable" value="1">
            <?php esc_html_e('Show as filter on archive pages', 'base-theme'); ?>
        </label>
        <p class="description">
            <?php esc_html_e('When enabled, this attribute appears in the product filter sidebar.', 'base-theme'); ?>
        </p>
    </div>
    <?php
});

// ── Admin: add checkbox to "Edit attribute" form ──────────────────────────────

add_action('woocommerce_after_edit_attribute_fields', function () {
    $attr_id  = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
    $attr     = $attr_id && function_exists('wc_get_attribute') ? wc_get_attribute($attr_id) : null;
    $slug     = $attr ? $attr->slug : '';
    $checked  = $slug && emsaks_is_attribute_filterable($slug);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="attribute_filterable">
                <?php esc_html_e('Show as filter', 'base-theme'); ?>
            </label>
        </th>
        <td>
            <input type="checkbox" name="attribute_filterable" id="attribute_filterable"
                   value="1" <?php checked($checked); ?>>
            <p class="description">
                <?php esc_html_e('When enabled, this attribute appears in the product filter sidebar on archive pages.', 'base-theme'); ?>
            </p>
        </td>
    </tr>
    <?php
});

// ── Save on attribute added ───────────────────────────────────────────────────

add_action('woocommerce_attribute_added', function ($id, $data) {
    if (!current_user_can('manage_woocommerce')) return;

    $map  = emsaks_get_filterable_attributes();
    $name = $data['attribute_name'] ?? '';

    if (!empty($_POST['attribute_filterable'])) {
        $map[$name] = true;
    } else {
        unset($map[$name]);
    }

    update_option('emsaks_attr_filterable', $map);
}, 10, 2);

// ── Save on attribute updated ─────────────────────────────────────────────────

add_action('woocommerce_attribute_updated', function ($id, $data, $old_slug) {
    if (!current_user_can('manage_woocommerce')) return;

    $map      = emsaks_get_filterable_attributes();
    $new_name = $data['attribute_name'] ?? '';

    // Clean up old slug if the name changed
    if ($old_slug && $old_slug !== $new_name) {
        unset($map[$old_slug]);
    }

    if (!empty($_POST['attribute_filterable'])) {
        $map[$new_name] = true;
    } else {
        unset($map[$new_name]);
    }

    update_option('emsaks_attr_filterable', $map);
}, 10, 3);

// ── Save on attribute deleted ─────────────────────────────────────────────────

add_action('woocommerce_attribute_deleted', function ($id, $name) {
    $map = emsaks_get_filterable_attributes();
    unset($map[$name]);
    update_option('emsaks_attr_filterable', $map);
}, 10, 2);
