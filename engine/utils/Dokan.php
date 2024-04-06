<?php

namespace engine\utils;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
use WeDevs\Dokan\Vendor\Vendor;
use WP_User;

defined('ABSPATH') || exit;

class Dokan
{
    /**
     * Returns ids of approved(enabled selling) vendors
     *
     * @param int $limit
     * @return array
     */
    public static function getVendorIDs(int $limit = -1): array
    {
        $builder = new QueryBuilder();

        //Get Vendor's IDs
        $IDs = $builder->select('user_id')
            ->from(Table::USERMETA)
            ->where('meta_key','=','dokan_enable_selling')
            ->andWhere('meta_value','=','yes')
            ->getColumn();

        return $limit >= 0 ? array_slice($IDs,0,$limit) : $IDs;
    }

    /**
     * Returns specified dokan vendor instance
     *
     * @param int|WP_User $user
     * @return Vendor
     */
    public static function getVendor(int|WP_User $user): Vendor
    {
        return new Vendor($user);
    }
}