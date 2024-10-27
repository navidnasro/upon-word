<?php

namespace engine\database\enums;

defined('ABSPATH') || exit;

enum Table: string
{
    case ACTIONSCHEDULER_ACTIONS = 'actionscheduler_actions';
    case ACTIONSCHEDULER_CLAIMS = 'actionscheduler_claims';
    case ACTIONSCHEDULER_GROUPS = 'actionscheduler_groups';
    case ACTIONSCHEDULER_LOGS = 'actionscheduler_logs';
    case COMMENTMETA = 'commentmeta';
    case COMMENTS = 'comments';
    case E_EVENTS = 'e_events';
    case LINKS = 'links';
    case OPTIONS = 'options';
    case POSTMETA = 'postmeta';
    case POSTS = 'posts';
    case TERM_RELATIONSHIPS = 'term_relationships';
    case TERM_TAXONOMY = 'term_taxonomy';
    case TERMMETA = 'termmeta';
    case TERMS = 'terms';
    case USERMETA = 'usermeta';
    case USERS = 'users';
    case WC_ADMIN_NOTE_ACTIONS = 'wc_admin_note_actions';
    case WC_ADMIN_NOTES = 'wc_admin_notes';
    case WC_CATEGORY_LOOKUP = 'wc_category_lookup';
    case WC_CUSTOMER_LOOKUP = 'wc_customer_lookup';
    case WC_DOWNLOAD_LOG = 'wc_download_log';
    case WC_ORDER_ADDRESSES = 'wc_order_addresses';
    case WC_ORDER_COUPON_LOOKUP = 'wc_order_coupon_lookup';
    case WC_ORDER_OPERATIONAL_DATA = 'wc_order_operational_data';
    case WC_ORDER_PRODUCT_LOOKUP = 'wc_order_product_lookup';
    case WC_ORDER_STATS = 'wc_order_stats';
    case WC_ORDER_TAX_LOOKUP = 'wc_order_tax_lookup';
    case WC_ORDERS = 'wc_orders';
    case WC_ORDERS_META = 'wc_orders_meta';
    case WC_PRODUCT_ATTRIBUTES_LOOKUP = 'wc_product_attributes_lookup';
    case WC_PRODUCT_DOWNLOAD_DIRECTORIES = 'wc_product_download_directories';
    case WC_PRODUCT_META_LOOKUP = 'wc_product_meta_lookup';
    case WC_RATE_LIMITS = 'wc_rate_limits';
    case WC_RESERVED_STOCK = 'wc_reserved_stock';
    case WC_TAX_RATE_CLASSES = 'wc_tax_rate_classes';
    case WC_WEBHOOKS = 'wc_webhooks';
    case WOOCOMMERCE_API_KEYS = 'woocommerce_api_keys';
    case WOOCOMMERCE_ATTRIBUTE_TAXONOMIES = 'woocommerce_attribute_taxonomies';
    case WOOCOMMERCE_DOWNLOADABLE_PRODUCT_PERMISSIONS = 'woocommerce_downloadable_product_permissions';
    case WOOCOMMERCE_LOG = 'woocommerce_log';
    case WOOCOMMERCE_ORDER_ITEMMETA = 'woocommerce_order_itemmeta';
    case WOOCOMMERCE_ORDER_ITEMS = 'woocommerce_order_items';
    case WOOCOMMERCE_PAYMENT_TOKENMETA = 'woocommerce_payment_tokenmeta';
    case WOOCOMMERCE_PAYMENT_TOKENS = 'woocommerce_payment_tokens';
    case WOOCOMMERCE_SESSIONS = 'woocommerce_sessions';
    case WOOCOMMERCE_SHIPPING_ZONE_LOCATIONS = 'woocommerce_shipping_zone_locations';
    case WOOCOMMERCE_SHIPPING_ZONE_METHODS = 'woocommerce_shipping_zone_methods';
    case WOOCOMMERCE_SHIPPING_ZONES = 'woocommerce_shipping_zones';
    case WOOCOMMERCE_TAX_RATE_LOCATIONS = 'woocommerce_tax_rate_locations';
    case WOOCOMMERCE_TAX_RATES = 'woocommerce_tax_rates';
}